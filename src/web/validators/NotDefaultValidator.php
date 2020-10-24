<?php
/**
 * Validate that the default option wasn't selected
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class NotDefaultValidator implements Validator
{

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate($field,$fields)
	{
		if($field->isDefault())
			throw new InvalidValueException('Invalid choice');
	}
}