<?php


namespace rizwanjiwan\common\web\visibilitychecks;


use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

interface VisibilityCheck
{

    /**
     * State if the given field should be visible or not
     * @param $field AbstractField to validate
     * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
     * @return boolean true if the field is visible or not given the passed information
     */
    public function isVisible(AbstractField $field,NameableContainer $fields):bool;

}