<?php
/**
 *A field which multiple items that could be selected
 */

namespace rizwanjiwan\common\web\fields;



class SelectFields extends AbstractField
{
	/** @var string[] of selected values  */
	private $values=array();//selected values
	/** @var string[] of default values  */
	private $defaults=array();
	/**@var $options SelectOption[] where string name is key*/
	private $options = array();



	/**
	 * Add a select option to this select input
	 * @param $selectOption SelectOption
	 * @return $this to allow easy chaining of calls
	 */
	public function addOption($selectOption)
	{
		//save the option
		$this->options[$selectOption->name]=$selectOption;

		//preselected and save as a default for later comparison
		if($selectOption->selectedByDefault)
		{
			array_push($this->values, $selectOption->name);
			array_push($this->defaults, $selectOption->name);
		}
		return $this;
	}

	public function getValuePrintable()
	{
		$retArray=array();
		foreach($this->values as $value)
			array_push($retArray,$this->options[$value]->friendlyName);
		return $retArray;
	}
	/**
	 * Get the options in this Select Input
	 * @return SelectOption[]
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Set the value of the input that was selected by the user
	 * @param $value string|string[]
	 */
	public function setValue($value)
	{
		if($value===null)
			$value=array();//empty array
		else if(is_array($value)===false)//it's a json string we need to convert to an an array
			$value=json_decode($value);
		$this->values = $value;
	}

	/**
	 * Find out if this input is empty
	 * @return bool true if empty
	 */
	public function isEmpty()
	{
		return count($this->values)===0;
	}

	/**
	 * Get back out a previously stored value.
	 * @return string[] value
	 */
	public function getValue()
	{
		return $this->values;
	}

	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault()
	{
		if(count($this->values)!==count($this->defaults))
			return false;
		//make sure every element matches
		foreach($this->values as $value)
		{
			if(array_search($value,$this->defaults)===false)
				return false;
		}
		return true;
	}

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray()
	{
		return true;
	}
}