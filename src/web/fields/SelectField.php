<?php
/**
 * Encapsulates a field with a fixed number of options.
 */

namespace rizwanjiwan\common\web\fields;



class SelectField extends AbstractField
{

	private $value = null;
	/**@var $options SelectOption[] where string name is key*/
	private $options = array();
	private $defaultValue=null;



	/**
	 * Add a select option to this select input
	 * @param $selectOption SelectOption
	 * @return $this for easy chaining
	 */
	public function addOption($selectOption)
	{
		//save the option
		$this->options[$selectOption->name]=$selectOption;

		if($selectOption->selectedByDefault)
		{
			$this->value=$selectOption->name;//set the preselected value
			$this->defaultValue=$selectOption->name;
		}
		return $this;
	}

	public function getValuePrintable()
	{
		$val=$this->getValue();
		if(array_key_exists($val,$this->options))
			return $this->options[$this->getValue()]->friendlyName;
		return $val;
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
		if(is_array($value))
			$value=implode(',',$value);
		if ((strcasecmp($value, 'null') === 0) || (strcasecmp($value, '') === 0))
			$this->value = null;
		else
			$this->value = $value;
	}

	/**
	 * Find out if this input is empty
	 * @return bool true if empty
	 */
	public function isEmpty()
	{
		return $this->value===null;
	}

	/**
	 * Get back out a previously stored value.
	 * @return string value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Find out if a given option was selected
	 * @param $name string name of the option
	 * @return bool true if selected
	 */
	public function isSelected($name)
	{
		return strcmp($name,$this->getValue())===0;
	}

	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault()
	{
		return strcmp($this->getValue(),$this->defaultValue)===0;
	}

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray()
	{
		return false;
	}
}