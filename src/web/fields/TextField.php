<?php
/**
 * For text fields
 */

namespace rizwanjiwan\common\web\fields;


class TextField extends AbstractField
{
	protected ?string $value=null;
	protected ?string $defaultValue=null;
	private bool $multiLine=false;

	public function __construct(string $uniqueName,$friendlyName,$defaultValue=null)
	{
		parent::__construct($uniqueName,$friendlyName);
		$this->defaultValue=$defaultValue;
		if((is_string($defaultValue))&&
			(strcasecmp($defaultValue,'')===0))
			$this->defaultValue=null;
		$this->value=$this->defaultValue;
	}

	/**
	 * Define if this text input be rendered as single or multi line? Defaults to false
	 * @param $val bool true if it should be multi line.
	 * @return $this for easy chaining
	 */
	public function setIsMultiLine(bool $val):self
	{
		$this->multiLine=$val;
		return $this;
	}

	/**
	 * Set the value of the input that was selected by the user.
	 * @param $value string to save
	 */
	public function setValue(mixed $value)
	{
		if(is_array($value))//convert the best we can to a string
		{
			$newString='';
			foreach($value as $el)
			{
				if(is_string($el))
					$newString.=$el;
			}
			$value=$newString;
		}
		if(strcasecmp($value,'')===0)
			$this->value=null;
		else
			$this->value=$value;
	}

	/**
	 * Get back out a previously stored value.
	 * @return string value
	 */
	public function getValue():string
	{
		return $this->value;
	}

	public function getValuePrintable():string
	{
		return $this->getValue();
	}
	/**
	 * Find out if this input is empty
	 * @return bool true if empty
	 */
	public function isEmpty():bool
	{
		return $this->value===null;
	}

	/**
	 * Should this text input be rendered as single or multi line?
	 * @return bool true if it should be multi line.
	 */
	public function isMultiline():bool
	{
		return $this->multiLine;
	}
	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault():bool
	{
		if($this->defaultValue===null)
			return $this->isEmpty();
		return strcmp($this->defaultValue,$this->getValue());
	}

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray():bool
	{
		return false;
	}
}