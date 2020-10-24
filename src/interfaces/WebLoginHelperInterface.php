<?php
/**
 * Common interfaces for loging in and logging out for web helpers
 */

namespace rizwanjiwan\common\interfaces;


use rizwanjiwan\common\classes\UserIdentity;

interface WebLoginHelperInterface
{

	/**
	 * Get the the html to render the login button
	 * @param $serviceReponseUrl string the URL on OUR server where the 3rd party response should be redirected to
	 * @return string the html to output
	 */
	public function getLoginHtml($serviceReponseUrl);

	/**
	 * Should be called on the $serviceResponseUrl from $this->getLoginHtml. Will set UserIdenity if successful.
	 * @return UserIdentity
	 * @throws \Exception on error/invalid login
	 */
	public function processServiceResponse();

	public function processLogout();

}