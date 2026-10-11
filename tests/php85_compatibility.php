<?php

declare(strict_types=1);

/**
 * Exercises the dependency entry points that must remain usable on PHP 8.5.
 *
 * This repository does not currently use a test framework, so the check is a
 * deliberately small executable regression test that fails on warnings,
 * deprecations, unexpected output, or missing imported classes.
 */

use GuzzleHttp\Client;
use Http\Promise\FulfilledPromise;
use Http\Promise\Promise;
use Leaf\Blade;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\Authentication\AccessTokenProvider;
use Microsoft\Kiota\Abstractions\Authentication\AllowedHostsValidator;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use rizwanjiwan\common\classes\EmailHelper;
use rizwanjiwan\common\classes\GoogleOpenIdProvider;
use rizwanjiwan\common\classes\PhoneHelper;
use rizwanjiwan\common\web\RequestHandler;

require_once dirname(__DIR__).'/vendor/autoload.php';

/**
 * Assert a compatibility check result.
 *
 * @param bool $condition The condition that must be true.
 * @param string $message The failure message to report.
 * @throws RuntimeException When the condition is false.
 */
function requireCompatibilityCheck(bool $condition, string $message): void
{
    if($condition===false)
        throw new RuntimeException($message);
}

set_error_handler(
    static function(int $severity, string $message, string $file, int $line): bool {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
);

$temporaryRoot=sys_get_temp_dir().'/commonpub-php85-'.bin2hex(random_bytes(8));
$cachePath=$temporaryRoot.'/cache';
$viewPath=$temporaryRoot.'/hello.blade.php';

try
{
    requireCompatibilityCheck(mkdir($cachePath, 0777, true), 'Unable to create the temporary Blade cache directory.');
    requireCompatibilityCheck(
        file_put_contents($viewPath, '<p>{{ $name }}</p>')!==false,
        'Unable to create the temporary Blade compatibility view.'
    );

    $blade=new Blade($temporaryRoot, $cachePath);
    requireCompatibilityCheck(
        $blade->render('hello', array('name'=>'PHP 8.5'))==='<p>PHP 8.5</p>',
        'Blade did not render the compatibility view as expected.'
    );

    requireCompatibilityCheck((new Client()) instanceof Client, 'Guzzle client could not be constructed.');
    requireCompatibilityCheck((new PHPMailer()) instanceof PHPMailer, 'PHPMailer could not be constructed.');
    requireCompatibilityCheck(
        (new GoogleOpenIdProvider(
            array(
                'clientId'=>'compatibility-test',
                'clientSecret'=>'compatibility-test',
                'redirectUri'=>'https://example.test/callback'
            )
        )) instanceof GoogleOpenIdProvider,
        'Google OAuth provider could not be constructed.'
    );
    requireCompatibilityCheck(
        (new PhoneHelper('4165551234'))->isValid(),
        'libphonenumber did not validate the compatibility number.'
    );
    requireCompatibilityCheck(
        (new EmailHelper('person@example.com'))->isValid(),
        'Email validation did not accept the compatibility address.'
    );

    $logger=new Logger('php85-compatibility');
    $logger->pushHandler(new NullHandler());
    $logger->info('Dependency compatibility smoke test.');
    requireCompatibilityCheck($logger->getName()==='php85-compatibility', 'Monolog could not write a test record.');

    $tokenProvider=new class implements AccessTokenProvider {
        private AllowedHostsValidator $allowedHostsValidator;

        public function __construct()
        {
            $this->allowedHostsValidator=new AllowedHostsValidator(array('graph.microsoft.com'));
        }

        public function getAuthorizationTokenAsync(string $url, array $additionalAuthenticationContext=[]): Promise
        {
            return new FulfilledPromise('compatibility-token');
        }

        public function getAllowedHostsValidator(): AllowedHostsValidator
        {
            return $this->allowedHostsValidator;
        }
    };
    requireCompatibilityCheck(
        GraphServiceClient::createWithAuthenticationProvider(
            new BaseBearerTokenAuthenticationProvider($tokenProvider)
        ) instanceof GraphServiceClient,
        'Microsoft Graph client could not be constructed.'
    );

    requireCompatibilityCheck(class_exists('Microsoft\\Graph\\GraphServiceClient'), 'Microsoft Graph was not autoloaded.');
    requireCompatibilityCheck(class_exists('Pheanstalk\\Pheanstalk'), 'Pheanstalk was not autoloaded.');
    requireCompatibilityCheck(class_exists('Sentry\\Client'), 'Sentry was not autoloaded.');
    requireCompatibilityCheck(RequestHandler::getUrl('missing')==='/404/', 'RequestHandler no longer returns its fallback URL.');

    echo "PHP 8.5 compatibility checks passed.\n";
}
finally
{
    restore_error_handler();
    if(file_exists($viewPath))
        unlink($viewPath);
    if(is_dir($cachePath))
    {
        $cachedFiles=scandir($cachePath);
        if($cachedFiles!==false)
        {
            foreach($cachedFiles as $cachedFile)
            {
                if(($cachedFile!=='.')&&($cachedFile!=='..'))
                    unlink($cachePath.'/'.$cachedFile);
            }
        }
        rmdir($cachePath);
    }
    if(is_dir($temporaryRoot))
        rmdir($temporaryRoot);
}
