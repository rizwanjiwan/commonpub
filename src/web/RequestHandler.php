<?php
/**
 * Handles web requests and passes them off to the controller
 */

namespace rizwanjiwan\common\web;

use Exception;
use rizwanjiwan\common\classes\Config;
use rizwanjiwan\common\classes\exceptions\RouteException;
use rizwanjiwan\common\classes\LogManager;
use function Sentry\init as sentryInit;
use function Sentry\captureException as sendExceptionToSentry;
use function Sentry\captureLastError as sendLastErrorToSentry;

class RequestHandler
{
	const SESSION_NAME='sessionid';
	/**
	 * @var array key=>value of string url and the associated Route.
	 */
	private array $routes=array();

	private $chainedExceptionHandler; //for chaining to the previous handler if we don't deal with it.

    private bool $exceptionSentToSentry=false;//true if we send something to sentry to avoid multiple sends.

	private array $errorsToFreakOutOver=array(
		E_ERROR				=>	'Error',
		E_PARSE				=>	'Parse Error',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_STRICT			=>	'Strict Warning',
		E_USER_ERROR		=>	'User Error'
	);

	private array $otherErrors=array(
		E_WARNING			=>	'Warning',
		E_NOTICE			=>	'Notice',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_RECOVERABLE_ERROR	=>	'Recoverable Error',
		E_DEPRECATED		=>	'Deprecated',
		E_USER_DEPRECATED	=>	'User',
		E_ALL				=>	'All Error'
	);

	public function __construct()
	{
		try
		{
			session_name(self::SESSION_NAME);
		}
		catch(Exception $e)
		{
			//nothing to do...
		}
	}

	private function createRequest():Request
	{
		$request=new Request();
		$request->log=LogManager::createLogger('Common');
		return $request;
	}
	/**
	 * @param $route Route to add
	 */
	public function addRoute(Route $route)
	{
		//route URLS for our purposes shouldn't start or end with a /
		$url=trim( $route->getUrl(), "/" );
		$this->routes[$url]=$route;

	}
	public function registerForShutdownErrors()
	{
		register_shutdown_function(array($this, 'handelShutdownError'));
        if(Config::getBool('SENTRY_ON'))//do other stuff for sentry
        {
            sentryInit([
                'dsn' => Config::get('SENTRY_DNS_PHP'),
                'environment'=>Config::get('ENV')
            ]);
            $this->chainedExceptionHandler = set_exception_handler(array($this, 'handleException'));
        }
	}

	public function handelShutdownError()
	{
		$error=error_get_last();
		if ($error===null)//nothing to do
			return;
		//get error type details
		$errorType=@$error['type'];
		$errorString='Error';
		$fatal=false;
		if(array_key_exists($errorType,$this->errorsToFreakOutOver))
		{
			$errorString=$this->errorsToFreakOutOver[$errorType];
			$fatal=true;
		}
		elseif(array_key_exists($errorType,$this->otherErrors))
			$errorString=$this->otherErrors[$errorType];
		$message="[$errorString] ".@$error['message'].' In file: '.@$error['file'].' on line: '.@$error['line'];

		//output
		$request=$this->createRequest();
		//log error
		LogManager::setLoggingOn();
		if($request->log!==null)
		{//we don't try to create the log because that might have been what started this error
			if($fatal)
				$request->log->error("Fatal: ".$message);
			else
				$request->log->error($message);
		}
		//output error
		if($fatal)
        {
            if($this->exceptionSentToSentry===false)
                sendLastErrorToSentry();
            $request->respondError($message);
        }
	}

    /**
     * @param $e Exception
     * @throws Exception
     */
    public function handleException($e)
    {
        sendExceptionToSentry($e);
        $this->exceptionSentToSentry=true;
        if ($this->chainedExceptionHandler!==null)
                call_user_func($this->chainedExceptionHandler, $e);
        else
                throw $e;
    }
	/*
	 * Routes are defined based on the request URI.
	 *
	 * They take the /folder1/folder2/folder3/.. request and convert them to
	 * new Folder1Controller->folder2Folder3($request)
	 * You can nest as deep as you want on the folders.
	 *
	 * The request for / loads the HomepageController->index($request) just as folder/
	 * leads to new Folder1Controller->index($request);
	 *
	 * REDIRECT_URL $_SERVER is used
	 */
	/**
	 * Handle a request
	 */
	public function handle()
	{
		//check if we should redirect to https first
		if(
			(Config::getBool('FORCE_HTTPS'))&&
			(
				(array_key_exists('HTTPS',$_SERVER)===false)||
				(strcmp($_SERVER["HTTPS"],"on")!==0)
			)
		)
		{
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"],true,301);
			return;
		}
		LogManager::setLoggingOn();
		$request=$this->createRequest();
		//look up controller, build up request, add filters, execute, deal with response.
		try
		{
			$controller=null;
			$methodName=null;
			$url='';
			if(array_key_exists('REDIRECT_URL',$_SERVER)!==false)
				$url=trim(trim($_SERVER['REDIRECT_URL'],'/'));
			if(array_key_exists($url,$this->routes)===false)
			{
				$request->respondError($url,404);
				return;
			}
			$route=$this->routes[$url];
			/**@var $route Route*/
			$request->routeParams=$route->getParameters();
			$route->doRouting($request);
		}
		catch(RouteException $e)
		{
			$request->log->error('Error: '.$e->getMessage()." -> ".$e->getTraceAsString());
			$request->respondError($e->getMessage(),500);
		}
	}
}