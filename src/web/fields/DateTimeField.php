<?php
/**
 * Encapsulates Date times
 */

namespace rizwanjiwan\common\web\fields;



use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use Carbon\Carbon;
use DateTime;

class DateTimeField extends AbstractField
{
	protected $value=null;
	protected $defaultValue=null;
	private $isNull=false;
	private $isInvalidDateTime=false;

	public function __construct($uniqueName,$friendlyName,$defaultValue=null)
	{
		parent::__construct($uniqueName,$friendlyName);
		$this->isNull=$defaultValue===null;
		if($this->isNull)
			$defaultValue=null;
		else//hopefully date string
			$defaultValue=new DateTime($defaultValue);
		$this->defaultValue=$defaultValue;
		$this->value=$this->defaultValue;
	}

	/**
	 * Set the value of the input that was selected by the user.
	 * @param $value mixed to save
	 * @return DateTimeField $this
	 */
	public function setValue($value)
	{
		$this->isNull=false;
		if($value instanceof DateTime)
		{
			$this->value=$value;
		}
		else if(($value===null)||(strlen($value)===0))
			$this->isNull=true;
		else
		{
			try
			{
				$this->value=new DateTime($value);
			}
			catch(\Exception $e)
			{
				$this->isNull=true;
				$this->isInvalidDateTime=true;
			}
		}

		return $this;
	}
	/**
	 * Validate this field
	 * @param $otherFields NameableContainer all the fields in case validation depends on another value
	 * @throws InvalidValueException
	 */
	public function validate($otherFields)
	{
		if($this->isInvalidDateTime)
			throw new InvalidValueException('Invalid date format');
		parent::validate($otherFields);
	}
	/**
	 * Get back out a previously stored value.
	 * @return string value
	 */
	public function getValue()
	{
		if($this->isNull)
			return null;
		return $this->value->format('Y-m-d H:i');
	}

	/**
	 * @return DateTime the date time contained in $this
	 */
	public function getDateTime()
	{
		return $this->value;
	}
	public function getValuePrintable()
	{
		if($this->isNull)
			return null;
		return Carbon::instance($this->value)->diffForHumans();
	}
	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault()
	{
		if($this->isNull)
			return $this->defaultValue===$this->value;
		return	$this->defaultValue->getTimestamp()===$this->value->getTimestamp();
	}

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray()
	{
		return false;
	}

	/**
	 * @return boolean return true if the value of this date time is really just "NULL"
	 */
	public function isNull()
	{
		return $this->isNull;
	}
}