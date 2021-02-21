<?php


namespace rizwanjiwan\common\web\visibilitychecks;


use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class FieldEqualsVisibilityCheck extends FieldNotEqualsVisibilityCheck
{
    /**
     * State if the given field should be visible or not
     * @param $field AbstractField to validate
     * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
     * @return boolean true if the field is visible or not given the passed information
     */
    public function isVisible(AbstractField $field, NameableContainer $fields):bool
    {
        return !parent::isVisible($field,$fields);
    }

    /**
     * Add a value to check the field against
     * @param $valueNotEqual string the value to be equal (yes, the name is the opposite, php 8)
     * @return self $this
     */
    public function addValue(string $valueNotEqual):self
    {
        parent::addValue($valueNotEqual);
        return $this;
    }
}