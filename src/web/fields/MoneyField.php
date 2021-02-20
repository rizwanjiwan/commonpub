<?php
/**
 * Field containing money
 */

namespace rizwanjiwan\common\web\fields;


class MoneyField  extends TextField
{


	public function getValuePrintable():string
	{
		$val=parent::getValuePrintable();
		if(strlen($val)===0)
			return '';//don't add text when there isn't text already there.
		if($val<0)
			return "$(". number_format($val*-1, 2).")";
		return "$". number_format($val, 2);
	}
}