<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\AbstractProvider;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Monolog\Logger;
use rizwanjiwan\common\classes\exceptions\AuthorizationException;
use rizwanjiwan\common\classes\exceptions\RejectedAuthorizationCodeException;
use rizwanjiwan\common\classes\GoogleOpenIdProvider;
use rizwanjiwan\common\classes\UserIdentity;
use rizwanjiwan\common\traits\AuthenticationTrait;
use rizwanjiwan\common\web\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

/** Run the shared callback with a real OAuth provider backed by queued HTTP responses. */
final class CallbackFixture
{
    use AuthenticationTrait { processCallback as public; }

    private AbstractProvider $provider;

    /**
     * @param AbstractProvider $provider Provider configured with the test HTTP client.
     * @param Logger $log Logger capturing only this callback's events.
     */
    public function __construct(AbstractProvider $provider, Logger $log)
    {
        $this->provider = $provider;
        $this->log = $log;
    }

    /**
     * Supply the test provider without reading application credentials.
     * @param int $method Sign-in method from the test route.
     * @param string $callback Registered callback URL.
     * @return AbstractProvider Prepared OAuth provider.
     */
    private function createProvider(int $method, string $callback): AbstractProvider
    {
        return $this->provider;
    }
}

/**
 * Verify one callback failure, including its sanitized exception and single log event.
 *
 * @param string $error Structured OAuth error code from the mock endpoint.
 * @param bool $afterExchange Whether the rejection comes from the later identity lookup.
 * @param bool $recoverable Whether this failure should ask for a fresh authorization code.
 * @return void
 * @throws RuntimeException When the callback succeeds or reports the wrong failure.
 */
function checkCallbackFailure(string $error, bool $afterExchange, bool $recoverable): void
{
    $headers = ['Content-Type' => 'application/json'];
    $responses = [];
    if ($afterExchange)
        $responses[] = new Response(200, $headers, '{"access_token":"private-token","token_type":"Bearer"}');
    $responses[] = new Response(400, $headers, json_encode([
        'error' => $error,
        'error_description' => 'invalid_grant private-description',
    ], JSON_THROW_ON_ERROR));

    $provider = new GoogleOpenIdProvider();
    $provider->setHttpClient(new Client(['handler' => new MockHandler($responses)]));
    $handler = new TestHandler();
    $fixture = new CallbackFixture($provider, new Logger('test', [$handler]));
    $_GET['code'] = 'private-code';
    $request = new Request();
    $request->routeParams = ['method' => UserIdentity::METHOD_GOOGLE, 'callback' => 'https://example.test/callback'];

    try {
        $fixture->processCallback($request);
        throw new RuntimeException('The callback should have failed.');
    } catch (AuthorizationException $e) {
        $records = $handler->getRecords();
        $level = $recoverable ? Level::Info : Level::Error;
        $operation = $recoverable ? 'invalid_grant' : ($afterExchange ? 'identity lookup' : 'authorization code exchange');
        if (($e instanceof RejectedAuthorizationCodeException) !== $recoverable
            || count($records) !== 1 || $records[0]->level !== $level
            || !str_contains($records[0]->message, $operation))
            throw new RuntimeException('Unexpected callback classification for '.$error.' during '.$operation.'.');
        if (str_contains(json_encode($records[0]->toArray(), JSON_THROW_ON_ERROR).$e->getMessage(), 'private-')
            || $e->getPrevious() !== null)
            throw new RuntimeException('Callback failure exposed provider details.');
    }
}

checkCallbackFailure('invalid_grant', false, true);
checkCallbackFailure('invalid_client', false, false);
checkCallbackFailure('invalid_grant', true, false);
echo "OAuth callback checks passed.\n";
