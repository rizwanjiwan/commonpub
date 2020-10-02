<?php
/**
 * Basic interface that indicates that a class has a "name" that can be used.
 */

namespace rizwanjiwan\common\interfaces;


interface Nameable
{
	/**
	 * A friendly name for the end user to see
	 * @return string friendly name
	 */
	public function getFriendlyName();

	/**
	 * A name for use that is unique
	 * @return string name
	 */
	public function getUniqueName();
}