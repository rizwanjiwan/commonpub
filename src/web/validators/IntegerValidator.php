<?php
/**
 * Confirm that an input value is an integer
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class IntegerValidator implements Validator
{

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field,NameableContainer $fields)
	{
		$value=$field->getValue();
		if(($value===null)||(strlen($value)===0))
			return;//nothing to check
		if(ctype_digit($value)===false)
			throw new InvalidValueException('Enter a whole number');
	}
}