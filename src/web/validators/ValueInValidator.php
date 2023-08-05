<?php

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

/**
 * Make sure that the value(s) are in a given range.
 */
class ValueInValidator implements Validator
{

    private array $values;
    private string $errorMsg;

    /**
     * @param array $values The valid values
     * @param string $errorMsg the error message to throw if the value isn't valid
     */
    public function __construct(array $values, string $errorMsg="Invalid value")
    {
        $this->values=$values;
        $this->errorMsg=$errorMsg;
    }

    public function validate(AbstractField $field, NameableContainer $fields)
    {
        $fieldValue=$field->getValue();
        if($fieldValue===null){
            return;
        }

        if($field->isValueArray()){
            foreach($fieldValue as $val){
                if(in_array($val,$this->values)===false){
                    throw new InvalidValueException($this->errorMsg);
                }
            }
            return;
        }
        if(in_array($fieldValue,$this->values)===false){
            throw new InvalidValueException($this->errorMsg);
        }
    }
}