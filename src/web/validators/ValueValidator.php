<?php
/**
 * Validate if a value is equal to something or not
 */

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class ValueValidator  implements Validator
{
	const CONDITION_EQUALS=1;
	const CONDITION_NOT_EQUALS=2;

	private int $condition=self::CONDITION_EQUALS;
	private string $message='Error';
	private string|array $value;

	public function __construct(string|array $value,int $condition, string $message='Error')
	{
		$this->value=$value;
		$this->condition=$condition;
		$this->message=$message;
	}

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate($field,$fields)
	{
		$value=$field->getValue();

		$equals=false;
		if(is_array($value))//check if the value of interest is in the array
			$equals=array_search($this->value,$value);
		else
			$equals=strcasecmp($this->value,$value)===0;
		if($equals)
		{
			if($this->condition===self::CONDITION_EQUALS)
				throw new InvalidValueException($this->message);
		}
		else
		{
			if($this->condition===self::CONDITION_NOT_EQUALS)
				throw new InvalidValueException($this->message);
		}

	}
}