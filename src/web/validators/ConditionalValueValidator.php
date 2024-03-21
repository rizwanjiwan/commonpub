<?php
/**
 * Execute a sub validator if a condition is met, otherwise validation fails.
 */
namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;

class ConditionalValueValidator implements Validator
{

	const CONDITION_EQUALS=1;
	const CONDITION_NOT_EQUALS=2;

	private string $fieldName;
	private int $condition=self::CONDITION_EQUALS;
	private ?string $value=null;
	/**
	 * @var Validator
	 */
	private Validator $followOnValidator;

	/**
	 * ConditionalValueValidator constructor.
	 * @param $fieldName string uniqueName of the field to check
	 * @param $condition int one of the CONDITION_* constants
	 * @param $value string the value to compare for or search for
	 * @param $followOnValidator Validator to execute if $fieldName condition value
	 */
	public function __construct(string $fieldName,int $condition,string $value,Validator $followOnValidator)
	{
		$this->fieldName=$fieldName;
		$this->condition=$condition;
		$this->value=$value;
		$this->followOnValidator=$followOnValidator;
	}


	/**
	 * Validate a field against this Validators criteria
	 * @param $field AbstractField to validate
	 * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
	 * @throws InvalidValueException if not valid
	 */
	public function validate(AbstractField $field, NameableContainer $fields)
	{
		$conditionField=$fields->get($this->fieldName);
		if($conditionField===null)
			throw new InvalidValueException($this->fieldName." not provided");
		/**@var $conditionField AbstractField*/
		$value=$conditionField->getValue();
        if($value===null){
            $value="";
        }
		$equals=false;
		if(is_array($value))//check if the value of interest is in the array
			$equals=array_search($this->value,$value);
		else
			$equals=strcasecmp($this->value,$value)===0;
		if($equals)
		{
			if($this->condition===self::CONDITION_EQUALS)
				$this->followOnValidator->validate($field,$fields);

		}
		else
		{
			if($this->condition===self::CONDITION_NOT_EQUALS)
				$this->followOnValidator->validate($field,$fields);
		}

	}

}