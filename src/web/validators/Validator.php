<?php


/**
 * Defines validation that can be done on form value to ensure it's valid
 */
namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

interface Validator
{

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate($field,$fields);
}