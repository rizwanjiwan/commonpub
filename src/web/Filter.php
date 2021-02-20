<?php
/**
 * Filters provide reusable code chunks that can filter traffic for different reasons (not auth, etc)
 */

namespace rizwanjiwan\common\web;


use rizwanjiwan\common\interfaces\Nameable;

interface Filter extends Nameable
{
	/**
	 * Filter this request. If a filter fails, the filter should use the request object to do what it needs to
	 * (e.g. output error, redirect to url, etc.) and stopping further execution if warented.
	 * @param $request Request the request
	 */
	public function filter(Request $request);
}