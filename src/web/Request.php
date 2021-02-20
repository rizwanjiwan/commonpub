<?php
/**
 * Provides Controllers information about a request, common variables needed and
 */

namespace rizwanjiwan\common\web;


use rizwanjiwan\common\classes\Config;
use rizwanjiwan\common\classes\NameableContainer;
use Exception;
use Jenssegers\Blade\Blade;
use Monolog\Logger;
use rizwanjiwan\common\web\helpers\Alert;

class Request
{

	/**
	 * @var string[] associate array of parameters from the route
	 */
	public array $routeParams=array();
	/**
	 * @var Logger to use
	 */
	public Logger $log;
	/**
	 * @var NameableContainer of Filter
	 */
	private NameableContainer $filters;

	/**
	 * @var string Stores additional context of what was going on when an error happened.
	 */
	private string $errorContext="Pre-response";

	public function __construct()
	{
		$this->filters=new NameableContainer();
	}

	/**
	 * Respond to the request with a Blade View
	 * @param $view string view name
	 * @param $data array()
	 */
	public function respondView(string $view,array $data=array())
	{
		$this->errorContext='View: '.$view;
		try
		{
			$this->respondViewThrowExceptions($view,$data);
		}
		catch(Exception $e)//dump exception
		{
			$this->log->error('Error rendering '.$view.": ".$e->getMessage()." -> ".$e->getTraceAsString());
			$this->respondError($e->getMessage(),404);
		}

	}

	/**
	 * Respond to the request with a Blade View
	 * @param $view string view name
	 * @param $data array()
	 * @throws Exception
	 */
	private function respondViewThrowExceptions(string $view, array $data=array())
	{
		//int blade
		$this->log->debug('Rendering '.$view);
		$cachePath=realpath(dirname(__FILE__)).Config::get('CACHE_DIR');
		if (!file_exists($cachePath)) {
			mkdir($cachePath, 0777, true);
		}
		$blade = new Blade(
			array(realpath(dirname(__FILE__)).Config::get('VIEW_DIR')),
			$cachePath);
		//output
		echo $blade->render($view,$data);
	}

	/**
	 * When you want to dump output as json
	 * @param $obj object|array which can be passed through json_encode
	 */
	public function respondJson(mixed $obj)
	{
		echo json_encode($obj);
	}

	/**
	 * When you want to redirect users elsewhere. You should consider creating an alert before doing this.
	 * Will end execution.
	 * @param $url string url to redirect to
	 * @param $code int http code for the redirect (302 - "temporary" or 301 - "permanent" are likely candidates)
	 */
	public function respondRedirect(string $url, int $code=302)
	{
		$this->errorContext='Redirect ('.$code.'): '.$url;

		header('Location: '.$url,true,$code);
	}

	/**
	 * When you're doing your own thing and don't want the framework to do anything for your.
	 */
	public function respondCustom()
	{
		$this->errorContext='Respond Custom: ';
		//nothing to do
	}

	/**
	 * A last resort error output method which doesn't rely on anything and just dumps the error to the browser.
	 * This method terminates all further execution.
	 * It will try to use the view errors.code and errors.default first but then resort to a failsafe output if those options don't exit.
	 * Views that are called will have an Alert they can work with which details the issue(s) and a data point 'code' cwith the error code.
	 * @param $message string message
	 * @param $code int the http error code, if any.
	 */
	public function respondError(string $message,int $code=500)
	{
		http_response_code($code);
		if($code!=404)
			$message.=' - '.$this->errorContext;
		if($code===404)
			$this->log->debug($message);
		else
			$this->log->error($message);
		try
		{
			new Alert(Alert::TYPE_DANGER,$message);
			$this->respondViewThrowExceptions('errors.'.$code,array('code'=>$code));
		}
		catch(Exception $e)
		{
			try
			{
				$this->respondViewThrowExceptions('errors.default',array('code'=>$code));

			}
			catch(Exception $e)
			{
				echo $message;
				exit(0);
			}
		}
	}

}