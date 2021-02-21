<?php

/**
 * Describes the general field which allows for input and output. Allows for special formatting and validation.
 */
namespace rizwanjiwan\common\web\fields;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\exceptions\NameableException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\validators\Validator;
use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\visibilitychecks\VisibilityCheck;

abstract class AbstractField implements Nameable
{
	use NameableTrait;

	private array $validators=array();

	private array $visibilityChecks=array();

	public function __construct(string $uniqueName,?string $friendlyName)
	{
		try
		{
			$this->setUniqueName($uniqueName);//never throws but need to surround
		} catch (NameableException $e)
		{
		}
		$this->setFriendlyName($friendlyName);
	}

	/**
	 * Add a validator that will later be used in the validate() call.
	 * @param $validator Validator to add to use later
	 * @return $this for easy chaining
	 */
	public function addValidator(Validator $validator)
	{
		array_push($this->validators,$validator);
		return $this;
	}

    /**
     * Validate this field
     * @param $otherFields NameableContainer all the fields in case validation depends on another value
     * @throws InvalidValueException
     */
	public function validate(NameableContainer $otherFields)
	{
		foreach($this->validators as $validator)
		{
			/**@var $validator Validator*/
			$validator->validate($this,$otherFields);
		}
	}

	/**
	 * Specify the visibility checks to execute on this AbstractField. If this method is never called, this field will always be visible. All visibility checks must pass for this field to be visible.
	 * @param $visibilityCheck VisibilityCheck a check to run.
	 * @return self $this
	 */
	public function addVisibilityCheck(VisibilityCheck $visibilityCheck):self
	{
		array_push($this->visibilityChecks,$visibilityCheck);
		return $this;
	}


	/**
	 * Find out if this field should show given the attached visibility checks
	 * @param $fields NameableContainer|null of fields filled in that sit along side this field. Null will skip checks.
	 * @return bool|null if this field should show. Null to not do anything with this field.
	 */
	public function isVisible(?NameableContainer $fields):?bool
	{
		if((count($this->visibilityChecks)===0)||($fields===null))
			return true;
		foreach($this->visibilityChecks as $visibilityCheck)
		{
			if($visibilityCheck->isVisible($this,$fields)===false)
				return false;//fail
		}
		return true;
	}

	public function isMultiline():bool
	{
		return false;
	}
	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public abstract function isValueArray():bool;

	/**
	 * Set the value of the input that was selected by the user
	 * @param $value mixed
	 */
	public abstract function setValue(mixed $value);

	/**
	 * Get back out a previously stored value.
	 * @return string|string[] value or array of values
	 */
	public abstract function getValue():string|array|null;

	/**
	 * Get back out a previously stored value in a human readable/friendly output format
	 * @return string|string[] value or array of values that are human readble/friendly/for the printable output
	 */
	public abstract function getValuePrintable():string|array|null;

	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public abstract function isDefault():bool;

}