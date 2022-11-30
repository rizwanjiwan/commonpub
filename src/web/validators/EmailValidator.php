<?php
/**
 * Validate a field input as an email address
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\EmailHelper;
use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class EmailValidator implements Validator
{
	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field, NameableContainer $fields)
	{
		$value=$field->getValue();
		if(strlen($value??"")===0)
			return;//nothing to check
		$helper=new EmailHelper($value);
		if($helper->isValid()===false)
			throw new InvalidValueException($helper->getInvalidFormatReason());
	}
}