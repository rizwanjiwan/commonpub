<?php
/**
 * For views
 */
namespace rizwanjiwan\common\web\routes;

use rizwanjiwan\common\classes\UserIdentity;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\Route;

class ViewRoute extends Route
{

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public function getType():int
	{
		return self::ROUTE_TYPE_VIEW;
	}

	/**
	 * Do the actual routing work
	 * @param $request Request
	 */
	public function doRouting(Request $request)
	{
		$request->log->debug('Route to view '.$this->getTarget());
		$request->respondView($this->getTarget(),
			array(
				'user'=>UserIdentity::singleton(),
				'routeParams'=>$request->routeParams)
		);
	}
}