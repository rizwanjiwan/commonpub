<?php
namespace rizwanjiwan\common\web\filters;

use rizwanjiwan\common\classes\UserIdentity;
use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\Filter;
use rizwanjiwan\common\web\helpers\Alert;
use rizwanjiwan\common\web\Request;


/**
 * Ensures users are authenticated, else take them to get authenticated.
 */

class AuthenticatedUserFilter implements Filter
{

	use NameableTrait;

	private string $redirectUrl;

	/**
	 * AuthenticatedUserFilter constructor.
	 * @param $redirectUrl string the URL to redirect the user to for the authentication trait controller to take over.
	 */
	public function __construct(string $redirectUrl='/auth/')
	{
		$this->redirectUrl=$redirectUrl;
	}

	/**
	 * Filter this request. If a filter fails, the filter should use the request object to do what it needs to
	 * (e.g. output error, redirect to url, etc.) and stopping further execution if warented.
	 * @param $request Request the request
	 */
	public function filter(Request $request)
	{
	    $user=UserIdentity::singleton();
		if($user->isAuthed()===false)
		{
			new Alert(Alert::TYPE_DANGER,'You must be logged in');
			$request->respondRedirect($this->redirectUrl.'?r='.urlencode($_SERVER['REQUEST_URI']));
			exit(0);
		}
	}


}