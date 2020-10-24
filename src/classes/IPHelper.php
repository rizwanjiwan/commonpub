<?php
/**
 * Helpers for ip addresses
 */

namespace rizwanjiwan\common\classes;



class IPHelper
{
	/**
	 * Get the client IP while accounting for Cloudflare proxy
	 * @return mixed
	 */
	public static function getClientIp()
	{
		if (array_key_exists('HTTP_CF_CONNECTING_IP',$_SERVER))
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		return $_SERVER['REMOTE_ADDR'];
	}
}