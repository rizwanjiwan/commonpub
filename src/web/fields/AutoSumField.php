<?php
/**
 * Sums the value of all the lookup fields to calculate the value of this field
 */

namespace rizwanjiwan\common\web\fields;



class AutoSumField extends AbstractAutoFillField
{

	private array $values=array();//key value of field names and their associated value to tally as we go

	/**
	 * AutoFillTextField constructor. This only works on one field
	 * @param $delegateField AbstractField we're wrapping in an autofill
	 * @param $lookupFields string[]  field names that when changed should trigger a lookup by the framework
	 */
	public function __construct(AbstractField $delegateField, array $lookupFields)
	{
		parent::__construct($delegateField, $lookupFields);
		foreach($lookupFields as $fieldName)
			$this->values[$fieldName]=0;
	}

	/**
	 * Do the lookup for the autofill value
	 * @param $fieldName string the name of the field that triggered this lookup
	 * @param $fieldValue string the field value we're using for our lookup
	 * @param $set boolean true if you want to set this value in the delegated field as well as look it up
	 * @return null|string|string[] the value
	 */
	public function determineValue(string $fieldName, string $fieldValue, bool$set = false):string|array|null
	{
		if(strlen($fieldValue)>0)//update this field value
		{
			if(is_numeric($fieldValue)!==false)
			{
				if(array_key_exists($fieldName,$this->values))
					$this->values[$fieldName]=floatval($fieldValue);
			}
		}
		$total=0;
		foreach($this->values as $val)
			$total+=$val;
		if($set)
			$this->setValue($total);
		return $total;
	}
}