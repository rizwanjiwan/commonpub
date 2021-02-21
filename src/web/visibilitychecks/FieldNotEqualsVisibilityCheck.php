<?php


namespace rizwanjiwan\common\web\visibilitychecks;


use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class FieldNotEqualsVisibilityCheck implements VisibilityCheck
{
    private AbstractField $fieldToCheck;

    private array $valuesToCheck=array();

    public function __construct(string $fieldToCheck)
    {
        $this->fieldToCheck=$fieldToCheck;
    }

    /**
     * Add a value to check the field against
     * @param $valueNotEqual string the value
     * @return self $this
     */
    public function addValue(string $valueNotEqual):self
    {
        array_push($this->valuesToCheck,$valueNotEqual);
        return $this;
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
        //has to not be equal to all possible options
        return array_search($value,$this->valuesToCheck)===false;
    }
}