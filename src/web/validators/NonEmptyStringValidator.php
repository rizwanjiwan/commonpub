<?php
/**
 * Validate that a field isn't empty
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class NonEmptyStringValidator implements Validator
{
	private string $errorMessage;

	/**
	 * NonEmptyStringValidator constructor.
	 * @param string $errorMessage the error message to display if this fails
	 */
	public function __construct(string $errorMessage='Can\'t be empty')
	{
		$this->errorMessage=$errorMessage;
	}

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field,NameableContainer $fields)
	{
		$val=$field->getValue();
		if(is_array($val))//convert the best we can to a string
		{
			$newString='';
			foreach($val as $el)
			{
				if(is_string($el))
					$newString.=$el;
			}
			$val=$newString;
		}
		if(($val===null)||(strlen($val)===0))
			throw new InvalidValueException($this->errorMessage);
	}
}