<?php
/**
 * Encapsulates basic CSFR token management and validation. Yes, I looked at 3rd party solutions but many were unneedely complex
 * or did more than just CSFR which is unneeded (like locking to the CLIENT IP--that's a problem if someone leaves work and goes home
 * with their browser window still open, or generate a new token for each page and destroy the old one which makes multiple tabs
 * not work sometimes).
 */

namespace rizwanjiwan\common\classes;


use Exception;

class Csrf
{
	const CSRF_TOKEN_KEY='__csfr_token';

	private $failureReason=null;

	public function __construct()
	{
		if(!isset($_SESSION))
			session_start();
	}

    /**
     * Get a token to use.
     * @return string the token
     * @throws Exception
     */
	public function getToken()
	{
		if (empty($_SESSION[self::CSRF_TOKEN_KEY]))
			$_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
		return $_SESSION[self::CSRF_TOKEN_KEY];
	}

	/**
	 * Vaidate a token provided by a form
	 * @param $token string the earlier provided token passed through the form.
	 * @return bool true if valid
	 */
	public function isValid($token)
	{
		$this->failureReason=null;
		if (empty($_SESSION[self::CSRF_TOKEN_KEY]))
		{
			$this->failureReason='CSRF token timed out (browser idle for too long?)';
			return false;//can't be valid if we never generated one.
		}
		if(strcmp($_SESSION[self::CSRF_TOKEN_KEY],$token)===0)
		{
			$this->failureReason='CSRF token missing';
			return true;
		}
		$this->failureReason='Invalid CSRF token';
		return false;//doesn't match
	}

	/**
	 * Validate a token sent by a post (uses super global $_POST)
	 * @return true if valid
	 */
	public function isValidPost()
	{
		return $this->check($_POST)||$this->check($_GET);

	}

	/**
	 * In the event of a failed CSFR check, get the failure reason
	 * @return null|string the reason why isValid or isValidPost Failed
	 */
	public function getFailureReason()
	{
		return $this->failureReason;
	}
	private function check($var)
	{
		return (array_key_exists(self::CSRF_TOKEN_KEY,$var))&&
		($this->isValid($var[self::CSRF_TOKEN_KEY]));
	}
}