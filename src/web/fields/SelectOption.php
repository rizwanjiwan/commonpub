<?php
/**
 * The component parts of a SelectField(s)
 */

namespace rizwanjiwan\common\web\fields;


use rizwanjiwan\common\classes\exceptions\NameableException;
use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\traits\NameableTrait;

class SelectOption implements Nameable
{
    use NameableTrait;
	public bool $selectedByDefault=false;

	public array $dataFields=array();
	/**
	 * SelectOption constructor.
	 * @param $name string unique internal name to use
	 * @param $friendlyName string user facing name to use
	 * @param bool $selectByDefault should this option be selected by default
	 */
	public function __construct(string $name,string $friendlyName,$selectByDefault=false)
	{
        try
        {
            $this->setUniqueName($name);//never throws but need to surround
        } catch (NameableException $e)
        {
        }
        $this->setFriendlyName($friendlyName);
		$this->selectedByDefault=$selectByDefault;
	}

	/**
	 * Add data fields that can be used in the interface
	 * @param $name string name of the data field
	 * @param $value string value of the data field
	 */
	public function addDataField(string $name,string $value)
	{
		$this->dataFields[$name]=$value;
	}
}