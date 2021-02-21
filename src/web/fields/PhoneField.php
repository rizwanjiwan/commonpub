<?php
/**
 * Provides special formatting for phone numbers. Really should be coupled with Phone Validator.
 */

namespace rizwanjiwan\common\web\fields;


use rizwanjiwan\common\classes\PhoneHelper;

class PhoneField  extends TextField
{
	/**
	 * Get back out a previously stored value.
	 * @return ?string value
	 */
	public function getValue():?string
	{
		$orginal=parent::getValue();
		if($orginal===null)
		    return null;
		return (new PhoneHelper($orginal))->getFormatted(PhoneHelper::FORMAT_NUMBERS_ONLY);
	}

	public function getValuePrintable():string
	{
		$orginal=parent::getValue();
        if($orginal===null)
            return '';
		return (new PhoneHelper($orginal))->getFormatted();
	}

}