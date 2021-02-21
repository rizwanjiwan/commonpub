<?php
/**
 * Encapsulates Date times
 */

namespace rizwanjiwan\common\web\fields;



use Exception;
use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use Carbon\Carbon;
use DateTime;

class DateTimeField extends AbstractField
{
	protected DateTime|null $value=null;
	protected DateTime|null $defaultValue=null;
	private bool $isNull=false;
	private bool $isInvalidDateTime=false;

    /**
     * DateTimeField constructor.
     * @param string $uniqueName
     * @param string $friendlyName
     * @param string|null $defaultValue
     * @throws Exception
     */
	public function __construct(string $uniqueName,string $friendlyName,?string $defaultValue=null)
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
	public function setValue(mixed $value):self
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
			catch(Exception $e)
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
	public function validate(NameableContainer $otherFields)
	{
		if($this->isInvalidDateTime)
			throw new InvalidValueException('Invalid date format');
		parent::validate($otherFields);
	}
	/**
	 * Get back out a previously stored value.
	 */
	public function getValue():?string
	{
		if($this->isNull)
			return null;
		return $this->value->format('Y-m-d H:i');
	}

	/**
	 * @return ?DateTime the date time contained in $this
	 */
	public function getDateTime():?DateTime
	{
		return $this->value;
	}
	public function getValuePrintable():string|null
	{
		if($this->isNull)
			return null;
		return Carbon::instance($this->value)->diffForHumans();
	}
	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault():bool
	{
		if($this->isNull)
			return $this->defaultValue===$this->value;
		return	$this->defaultValue->getTimestamp()===$this->value->getTimestamp();
	}

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray():bool
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