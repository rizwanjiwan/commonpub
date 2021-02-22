<?php


namespace rizwanjiwan\common\traits;


use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\GoogleUser;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;
use Monolog\Logger;
use rizwanjiwan\common\classes\Config;
use rizwanjiwan\common\classes\exceptions\AuthorizationException;
use rizwanjiwan\common\classes\GoogleOpenIdProvider;
use rizwanjiwan\common\classes\LogManager;
use rizwanjiwan\common\classes\UserIdentity;
use rizwanjiwan\common\web\Request;

trait AuthenticationTrait
{

    /**
     * @var Logger
     */
    private Logger $log;

    public function __construct()
    {
        $this->log=LogManager::createLogger('AuthTrait');
    }

    public static function getRouteParamMethodName():string
    {
        return 'method';
    }
    public static function getRouteParamCallbackName():string
    {
        return 'callback';
    }
    /**
     * Start the login process. Send users here off the button click to log in
     * @param $request Request. Should specify the self::getRouteParamMethodName()=>UserIdentity.METHOD_* and self::getRouteParamCallbackName()=>callback url in the route params
     */
    public function login(Request $request)
    {
        $routeParams=$request->routeParams;
        $provider=$this->createProvider($routeParams[self::getRouteParamMethodName()],$routeParams[self::getRouteParamCallbackName()]);
        $_SESSION['oauth2state'] = $provider->getState();
        //echo $provider->getAuthorizationUrl();
        header('Location: '.$provider->getAuthorizationUrl());
        $request->respondCustom();
    }

    protected function processLogout(Request $request)
    {
        $identity=UserIdentity::singleton();
        $identity->clearIdentity();
    }

    /**
     * End the login process here. Will fill in UserIdentity for the user if login is successful... or Exception if shit got real.
     * @param $request Request. Should specify the self::getRouteParamMethodName()=>UserIdentity.METHOD_* and self::getRouteParamCallbackName()=>callback url in the route params
     * @throws AuthorizationException
     */
    protected function processCallback(Request $request)
    {
        if(array_key_exists('code',$_GET)===false)
            throw new AuthorizationException('Bad callback. Are you sure you clicked the login button?');
        try
        {
            $routeParams = $request->routeParams;
            $method = $routeParams[self::getRouteParamMethodName()];
            $callback = $routeParams[self::getRouteParamCallbackName()];
            $provider = $this->createProvider($method, $callback);
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
            if ($method === UserIdentity::METHOD_GOOGLE)
            {
                $userGoogle = $provider->getResourceOwner($token);
                /**@var $userGoogle GoogleUser */
                // Use these details to setup the identity
                $identity = UserIdentity::singleton();
                $this->log->info('Setting details: '.$userGoogle->getName()." ".$userGoogle->getEmail().' '.$userGoogle->getAvatar());
                $identity->setIdentity(
                    $userGoogle->getName(),
                    $this->emailToDomain($userGoogle->getEmail()),
                    $userGoogle->getEmail(),
                    $userGoogle->getAvatar(),
                    UserIdentity::METHOD_GOOGLE);
                // Use this to interact with an API on the users behalf
                $request->respondCustom();
                return;
            } else if ($method === UserIdentity::METHOD_AZURE_AD)
            {
                $graph = new Graph();
                $graph->setAccessToken($token->getToken());

                try
                {
                    $userAd = $graph->createRequest('GET', '/me')
                        ->setReturnType(User::class)
                        ->execute();
                } catch (GraphException $e)
                {
                    throw new AuthorizationException($e->getMessage(), 0, $e);
                }
                $identity = UserIdentity::singleton();
                $identity->setIdentity(
                    $userAd->getDisplayName(),
                    $this->emailToDomain($userAd->getMail()),
                    $userAd->getMail(),
                    null,
                    UserIdentity::METHOD_AZURE_AD);
                $request->respondCustom();
                return;
            }
        }
        catch(AuthorizationException $e)
        {
            $this->log->error('Provider '.$routeParams[self::getRouteParamMethodName()].' error: '.$e->getMessage());
            throw $e;
        }
        catch(Exception $e)
        {
            $this->log->error('Provider '.$routeParams[self::getRouteParamMethodName()].' error: '.$e->getMessage());
            throw new AuthorizationException('Error: '.$e->getMessage());
        }
        throw new AuthorizationException('Invalid sign-in method: '.$method);

    }

    /**
     * Get the domain part of an email address
     * @param $email ?string email
     * @return string the domain part of the email
     * @throws AuthorizationException on invalid email
     */
    private function emailToDomain(?string $email):string
    {
        if(($email===null)||(strlen($email===0))||(strpos($email,'@')===false))
            throw new AuthorizationException('Invalid email "'.$email.'" ');
        $parts=explode('@',$email);
        return $parts[1];
    }
    /**
     * @param $method int UserIdentity::METHOD_*
     * @param $callback string url to send the callback.
     * @return AbstractProvider|null Null if the method isn't valid
     */
    private function createProvider(int $method,string $callback):?AbstractProvider
    {
        $provider=null;
        if($method===UserIdentity::METHOD_GOOGLE)
        {
            $this->log->info('Creating Google OpenID Provider');
            $provider = new GoogleOpenIdProvider(array(
                'clientId'     => Config::get('OAUTH_GOOGLE_CLIENT_ID'),
                'clientSecret' => Config::get('OAUTH_GOOGLE_CLIENT_SECRET'),
                'redirectUri'  => $callback,
            ));
        }
        elseif($method===UserIdentity::METHOD_AZURE_AD)
            $provider = new GenericProvider(array(
                'clientId'                => Config::get('OAUTH_MS_CLIENT_ID'),
                'clientSecret'            => Config::get('OAUTH_MS_CLIENT_SECRET'),
                'redirectUri'             => $callback,
                'urlAuthorize'            => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                'urlAccessToken'          => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes'                  => 'openid profile offline_access User.Read'
            ));
        return $provider;
    }
}