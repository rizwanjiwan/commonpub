<?php
/**
 * For redirects
 */
namespace rizwanjiwan\common\web\routes;

use rizwanjiwan\common\web\helpers\Alert;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\Route;

class RedirectRoute extends Route
{

	private $code=301;
	private $alert=null;
	private $alertType=Alert::TYPE_INFO;

	/**
	 * Route constructor. Defaults to 301 redirect. Pass 'code'=>[int] as a parameter for a different redirect code like 302. You can also throw up an alert when this redirect happens with 'alert'=>string and 'alert_type'=>Alert::TYPE_*.
	 * @param $url string the url that this route is for
	 * @param $target string the target this route is for. For views it's the Blade notation to get to the blade file. For controllers is Class.method. For redirects it's the redirect to send to the $request->redirect
	 * @param $parameters string[] associative key value pairs. They get passed to Controllers in the $request->routeParams and to views as 'routeParams.'
	 */
	public function __construct($url,$target,$parameters=array())
	{
		parent::__construct($url,$target,$parameters);
		if(array_key_exists('code',$parameters))
			$this->code=$parameters['code'];
		if(array_key_exists('alert',$parameters))
			$this->alert=$parameters['alert'];
		if(array_key_exists('alert_type',$parameters))
			$this->alertType=$parameters['alert_type'];
	}

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public function getType()
	{
		return self::ROUTE_TYPE_REDIRECT;
	}

	/**
	 * Do the actual routing work
	 * @param $request Request
	 */
	public function doRouting($request)
	{
		if($this->alert!==null)
			new Alert($this->alertType,$this->alert);
		$request->respondRedirect($this->getTarget(),$this->code);
	}
}