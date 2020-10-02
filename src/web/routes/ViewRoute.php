<?php
/**
 * For views
 */
namespace rizwanjiwan\common\web\routes;

use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\Route;

class ViewRoute extends Route
{

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public function getType()
	{
		return self::ROUTE_TYPE_VIEW;
	}

	/**
	 * Do the actual routing work
	 * @param $request Request
	 */
	public function doRouting($request)
	{
		$request->log->debug('Route to view '.$this->getTarget());
		$request->respondView($this->getTarget(),
			array(
				'user'=>$request->user,
				'routeParams'=>$request->routeParams)
		);
	}
}