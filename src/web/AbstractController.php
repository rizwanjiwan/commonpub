<?php
/**
 * Created by PhpStorm.
 */

namespace rizwanjiwan\common\web;


use rizwanjiwan\common\classes\exceptions\RouteException;
use rizwanjiwan\common\classes\NameableContainer;

/**
 * Class AbstractController is what every controller should implement. The main thing is to provide functions for
 * each url request.
 *
 * They take the /folder1/folder2/folder3/.. request and convert them to
 * new Folder1Controller->folder2Folder3($request)
 *
 * Each method should do filtering first (add/remove filters as needed and then run them).
 *
 */
abstract class AbstractController
{

	/**
	 * @param $request Request
	 * @param $methodName string the method to invoke
	 * @throws RouteException
	 */
	public final function handle($request,$methodName)
	{
		if(method_exists($this,$methodName)===false)
			throw new RouteException("$methodName unknown");
		//run filters
		$filters=$this->getFilters();
		foreach($filters as $filter)/**@var $filter Filter*/
			$filter->filter($request);
		//hand off
		$this->$methodName($request);
	}

	/**
	 * Get the default set of filters that will be prefilled in a Request. You can modify these using the
	 * appropriate methods in Request before calling the filters to be run in Request.
	 * @return NameableContainer of Filter to have setup by default in each request
	 */
	public abstract function getFilters();

}