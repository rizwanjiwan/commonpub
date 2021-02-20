<?php
/**
 * For controllers
 */
namespace rizwanjiwan\common\web\routes;

use rizwanjiwan\common\classes\Config;
use rizwanjiwan\common\classes\exceptions\RouteException;
use rizwanjiwan\common\web\AbstractController;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\Route;

class ControllerRoute extends Route
{

	/**
	 * @return int the type of Route this is (ROUTE_TYPE_* constants)
	 */
	public function getType():int
	{
		return self::ROUTE_TYPE_CONTROLLER;
	}

	/**
	 * Do the actual routing work
	 * @param $request Request
	 * @throws RouteException on error
	 */
	public function doRouting(Request $request)
	{
		$request->log->debug('Route to controller '.$this->getTarget());
		$targetParts=explode('.',$this->getTarget());
		if(count($targetParts)!==2)//both class and method
			throw new RouteException('Invalid controller target: '.$this->getTarget());
		$fullClassName=$targetParts[0];//assume they provided the fully qualified class name
		if(strpos($targetParts[0],'\\')===false)	//they've provided the short hand path to the class
			$fullClassName=Config::get('CONTROLER_NAMESPACE').$targetParts[0];
		$methodName=$targetParts[1];
		if(class_exists($fullClassName)===false)
			throw new RouteException($fullClassName." not found.");
		$controller=new $fullClassName();
		if($controller instanceof AbstractController)
			$controller->handle($request,$methodName);
		else
			throw new RouteException("All controllers must be AbstractControllers");
	}
}