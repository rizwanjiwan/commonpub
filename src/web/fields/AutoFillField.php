<?php
/**
 * Auto fills the text Value based on a lookup against another field. You specify the outcome value this field
 * spits out by using lookup values (where if $lookupfield equals $fieldValue, we return $thisValue for autofill)
 */

namespace rizwanjiwan\common\web\fields;



class AutoFillField extends AbstractAutoFillField
{
	private array $lookupValues=array();
	private string|array|null $default;	//the default value if nothing is found from lookup

	/**
	 * AutoFillTextField constructor. This only works on one field
	 * @param $delegateField AbstractField we're wrapping in an autofill
	 * @param $lookupField string unique name of the lookup field
	 * @param string|array|null $default a value to use as a default if the lookup returns nothing
	 */
	public function __construct(AbstractField $delegateField, string $lookupField,string|array|null $default=null)
	{
		parent::__construct($delegateField, array($lookupField));
		$this->default=$default;
	}

	/**
	 * Add a key value pair to do lookups
	 * @param $fieldValue string the value of the lookup field
	 * @param $thisValue string|string[] the value that this should take if that lookup field is equal to $fieldValue
	 * @return AutoFillField $this
	 */
	public function addLookup(string $fieldValue, string|array $thisValue):self
	{
		$this->lookupValues[$fieldValue]=$thisValue;
		return $this;
	}

	/**
	 * Do the lookup for the autofill value
	 * @param $fieldName string the name of the field that triggered this lookup
	 * @param $fieldValue string the field value we're using for our lookup
	 * @param $set boolean true if you want to set this value in the delegated field as well as look it up
	 * @return null|string|string[] the value
	 */
	public function determineValue(string $fieldName, string $fieldValue, bool $set = false):string|array|null
	{
		$val=$this->default;
		if(array_key_exists($fieldValue,$this->lookupValues))
			$val=$this->lookupValues[$fieldValue];
		if($set)
			$this->setValue($val);
		return $val;
	}
}
