<?php
/**
 * Store a percentage
 */

namespace rizwanjiwan\common\web\fields;


class PercentField extends TextField
{


	public function getValuePrintable():string
	{
		$val=parent::getValuePrintable();
		if(strlen($val)===0)
			return '';//don't add text when there isn't text already there.
		return $val."%";
	}
}