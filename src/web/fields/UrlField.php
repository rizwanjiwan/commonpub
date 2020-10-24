<?php
/**
 * Holds a url in the form http://domain.com/something/ or https://domain.com/something/ or domain.com/something/.
 * If no protocol provided, it will shove http:// in front.
 */
namespace rizwanjiwan\common\web\fields;


class UrlField  extends TextField
{


	public function getValuePrintable()
	{
		$val=parent::getValuePrintable();
		if(strlen($val)===0)
			return '';//don't add text when there isn't text already there.
		//check if it is a valid url already:
		if(filter_var($val, FILTER_VALIDATE_URL)!==false)
			return $val;
		return "http://".$val;
	}
}