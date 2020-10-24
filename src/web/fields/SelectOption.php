<?php
/**
 * The component parts of a SelectField(s)
 */

namespace rizwanjiwan\common\web\fields;


class SelectOption
{
	public $name=null;
	public $friendlyName=null;
	public $selectedByDefault=false;

	public $dataFields=array();
	/**
	 * SelectOption constructor.
	 * @param $name string unique internal name to use
	 * @param $friendlyName string user facing name to use
	 * @param bool $selectByDefault should this option be selected by default
	 */
	public function __construct($name,$friendlyName,$selectByDefault=false)
	{
		$this->name=$name;
		$this->friendlyName=$friendlyName;
		$this->selectedByDefault=$selectByDefault;
	}

	/**
	 * Add data fields that can be used in the interface
	 * @param $name string name of the data field
	 * @param $value string value of the data field
	 */
	public function addDataField($name,$value)
	{
		$this->dataFields[$name]=$value;
	}
}