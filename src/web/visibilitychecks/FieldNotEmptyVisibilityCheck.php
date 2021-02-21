<?php


namespace rizwanjiwan\common\web\visibilitychecks;


use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class FieldNotEmptyVisibilityCheck implements VisibilityCheck
{
    private string $fieldToCheck;

    public function __construct(string $fieldToCheck)
    {
        $this->fieldToCheck=$fieldToCheck;
    }
    /**
     * State if the given field should be visible or not
     * @param $field AbstractField to validate
     * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
     * @return boolean true if the field is visible or not given the passed information
     */
    public function isVisible(AbstractField $field, NameableContainer $fields):bool
    {
        $fieldToCheck=$fields->get($this->fieldToCheck);
        if($fieldToCheck===null)
            return false;
        /**@var $fieldToCheck AbstractField*/
        $value=$fieldToCheck->getValue();
        if(is_array($value))
            return count($value)>0;	//has at least 1 element
        //has some string
        return ($value!==null)&&(strlen(trim($value))>0);
    }
}