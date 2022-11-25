<?php
/**
 * Validate that an input value isn't too long
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class MaxStringLengthValidator implements Validator
{
	private int $maxLength=9999999999;

	public function __construct(int $maxLength)
	{
		$this->maxLength=$maxLength;
	}

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field, NameableContainer $fields)
	{
		$len=strlen($field->getValue()??"");
		if($len>$this->maxLength)
			throw new InvalidValueException('Maximum is '.$this->maxLength.' characters');
	}
}