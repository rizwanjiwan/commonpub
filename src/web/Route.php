<?php
/**
 * Defines how to render for a given set of urls
 */

namespace rizwanjiwan\common\web;



use rizwanjiwan\common\classes\exceptions\RouteException;

abstract class Route
{

	/**
	 * Routes to a controller
	 */
	const ROUTE_TYPE_CONTROLLER=0;
	/**
	 * Bypasses a controller and goes straight to a view
	 */
	const ROUTE_TYPE_VIEW=1;

	/**
	 * This route should redirect to the target
	 */
	const ROUTE_TYPE_REDIRECT=2;

	/**
	 * @var string
	 */
	private $url;
	/**
	 * @var string
	 */
	private $target;
	/**
	 * @var string[] associative key=>value pairs
	 */
	private $parameters;

	/**
	 * Route constructor.
	 * @param $url string the url that this route is for
	 * @param $target string the target this route is for. For views it's the Blade notation to get to the blade file. For controllers is Class.method. For redirects it's the redirect to send to the $request->redirect
	 * @param $parameters string[] associative key value pairs. They get passed to Controllers in the $request->routeParams and to views as 'routeParams.'
	 */
	public function __construct($url,$target,$parameters=array())
	{
		$this->url=$url;
		$this->target=$target;
		$this->parameters=$parameters;
	}

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public abstract function getType();

	/**
	 * Do the actual routing work
	 * @param $request Request
	 * @throws RouteException on error
	 */
	public abstract function doRouting($request);

	/**
	 * @return string the url that this route is for
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return string[] associative key value pairs. They get passed to Controllers in the $request->routeParams and to views as 'routeParams.'
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @return string the target this route is for. For views it's the Blade notation to get to the blade file. For controllers is Class.method.
	 */
	public function getTarget()
	{
		return $this->target;
	}
}