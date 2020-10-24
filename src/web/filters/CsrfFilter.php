<?php
/**
 * CSRF protection filter on normal requests (POST/GET)
 */

namespace rizwanjiwan\common\web\filters;


use rizwanjiwan\common\classes\Csrf;
use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\Filter;
use rizwanjiwan\common\web\helpers\Alert;
use rizwanjiwan\common\web\Request;

class CsrfFilter implements Filter
{
	use NameableTrait;

	private $redirect;

	/**
	 * CsfrFilter constructor.
	 * @param string|object $response where you want to redirect the user to if this fails. If object, assumes you want to output a json error.
	 */
	public function __construct($response='/')
	{
		$this->redirect=$response;
	}

	/**
	 * Filter this request. If a filter fails, the filter should use the request object to do what it needs to
	 * (e.g. output error, redirect to url, etc.) and stopping further execution if warented.
	 * @param $request Request the request
	 */
	public function filter($request)
	{
		$csrf=new Csrf();

		if($csrf->isValidPost()===false)
		{
			if(is_object($this->redirect))
			{
				$this->redirect->error=$csrf->getFailureReason();
				$request->respondJson($this->redirect);
			}
			else
			{
				new Alert(Alert::TYPE_DANGER,$csrf->getFailureReason());
				$request->respondRedirect($this->redirect);
			}
			exit(0);
		}
	}
}