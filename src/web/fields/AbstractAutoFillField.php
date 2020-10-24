<?php
/**
 * A field that has a "calculation" ability which derives it's value from other (lookup) fields and save's it value in a
 * delegated field
 */

namespace rizwanjiwan\common\web\fields;



abstract class AbstractAutoFillField extends AbstractField
{
	/**
	 * @var AbstractField the field that this is wrapping and delegating everything to
	 */
	protected $delegateField;
	/**
	 * @var string[] of field names that when changed should trigger a lookup
	 */
	protected $lookupFieldNames;

	/**
	 * AutoFillTextField constructor.
	 * @param $delegateField AbstractField we're wrapping in an autofill
	 * @param $lookupFieldNames string[] of field names that when changed should trigger a lookup by the framework
	 */
	public function __construct($delegateField,$lookupFieldNames)
	{
		parent::__construct($delegateField->getUniqueName(), $delegateField->getFriendlyName());
		$this->lookupFieldNames=$lookupFieldNames;
		$this->delegateField=$delegateField;
	}

	/**
	 * @return string[] the unique names of the field that lookups should be triggered for
	 */
	public function getLookupFields()
	{
		return $this->lookupFieldNames;
	}
	
	/**
	 * Do the lookup for the autofill value
	 * @param $fieldName string the name of the field that triggered this lookup
	 * @param $fieldValue string the field value we're using for our lookup
	 * @param $set boolean true if you want to set this value in the delegated field as well as look it up
	 * @return null|string|string[] the value
	 */
	public abstract function determineValue($fieldName, $fieldValue, $set=false);

	/**
	 * Get the type of value stored
	 * @return boolean true if array for getValue, setValue, and getValuePrintable
	 */
	public function isValueArray()
	{
		return $this->delegateField->isValueArray();
	}

	/**
	 * Set the value of the input that was selected by the user
	 * @param $value mixed
	 * @return AutoFillField
	 */
	public function setValue($value)
	{
		return $this->delegateField->setValue($value);
	}

	/**
	 * Get back out a previously stored value.
	 * @return string|string[] value or array of values
	 */
	public function getValue()
	{
		return $this->delegateField->getValue();
	}

	/**
	 * Get back out a previously stored value in a human readable/friendly output format
	 * @return string|string[] value or array of values that are human readble/friendly/for the printable output
	 */
	public function getValuePrintable()
	{
		return $this->delegateField->getValuePrintable();
	}

	/**
	 * Find out if the value stored in the Input is the default value
	 * @return boolean true if default
	 */
	public function isDefault()
	{
		return $this->delegateField->isDefault();
	}

	public function isMultiline()
	{
		return $this->delegateField->isMultiline();
	}
}