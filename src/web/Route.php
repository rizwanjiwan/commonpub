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
	private string $url;
	/**
	 * @var string
	 */
	private string $target;
	/**
	 * @var string[] associative key=>value pairs
	 */
	private array $parameters;

    /**
     * @var Filter[] $filters filters to run on this route
     */
    private array $filters=array();

	/**
	 * Route constructor.
	 * @param $url string the url that this route is for
	 * @param $target string the target this route is for. For views it's the Blade notation to get to the blade file. For controllers is Class.method. For redirects it's the redirect to send to the $request->redirect
	 * @param $parameters string[] associative key value pairs. They get passed to Controllers in the $request->routeParams and to views as 'routeParams.'
	 */
	public function __construct(string $url, string $target, array $parameters=array())
	{
        header('X-Frame-Options: DENY');
		$this->url=$url;
		$this->target=$target;
		$this->parameters=$parameters;
	}

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public abstract function getType():int;

    /**
     * Do the  routing work
     * @param $request Request
     * @throws RouteException on error
     */
    public function doRouting($request):void
    {
        foreach($this->filters as $filter)
            $filter->filter($request);
        $this->doRoutingImp($request);
    }
	/**
	 * Do the actual routing work by implemenations
	 * @param $request Request
	 * @throws RouteException on error
	 */
	protected abstract function doRoutingImp(Request $request);

	/**
	 * @return string the url that this route is for
	 */
	public function getUrl():string
	{
		return $this->url;
	}

    /**
     * Add a route specific filter
     * @param Filter $routeSpecificFilter
     * @return $this
     */
    public function addFilter(Filter $routeSpecificFilter):self
    {
        array_push($this->filters,$routeSpecificFilter);
        return $this;
    }

	/**
	 * @return string[] associative key value pairs. They get passed to Controllers in the $request->routeParams and to views as 'routeParams.'
	 */
	public function getParameters():array
	{
		return $this->parameters;
	}

	/**
	 * @return string the target this route is for. For views it's the Blade notation to get to the blade file. For controllers is Class.method.
	 */
	public function getTarget():string
	{
		return $this->target;
	}
}