<?php
/**
 * Make sure a field value is greater than or equal to zero
 */

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class PositiveValueValidator implements Validator
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

		if($value<0)
			throw new InvalidValueException("Must not be a negative number");

	}
}