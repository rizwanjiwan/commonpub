<?php
/**
 * Validate a value as a phone number
 */

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\classes\PhoneHelper;

class PhoneValidator implements Validator
{
	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate($field, $fields)
	{
		$value=$field->getValue();
		if(strlen($value)===0)
			return;//nothing to check
		$helper=new PhoneHelper($value);
		if($helper->isValid()===false)
			throw new InvalidValueException($helper->getInvalidFormatReason());
	}
}