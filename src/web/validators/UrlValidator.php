<?php
/**
 *  Validates a url with or without the prefixed protocol
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class UrlValidator implements Validator
{


	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate($field, $fields)
	{
		$val=trim($field->getValue());
		if(strlen($val)===0)
			return;
		if(filter_var($val, FILTER_VALIDATE_URL)===false)
		{
			$val='http://'.$val;//try slapping a protocol on it. That might fix it.
			if(filter_var($val, FILTER_VALIDATE_URL)===false)
				throw new InvalidValueException('Invalid URL');
		}
	}
}