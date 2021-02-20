<?php
/**
 * Validate that an input is between a range
 */

namespace rizwanjiwan\common\web\validators;


use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;


class NumericRangeValidator  implements Validator
{
	private float|int $max;
	private float|int $min;

	public function __construct(float|int $min,float|int $max)
	{
		$this->max=$max;
		$this->min=$min;
	}

	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field, NameableContainer $fields)
	{
		$value=$field->getValue();

		if(($value>$this->max)||($value<$this->min))
			throw new InvalidValueException("Must be between {$this->min} and {$this->max}");

	}
}