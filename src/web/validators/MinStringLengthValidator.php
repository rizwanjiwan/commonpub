<?php

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class MinStringLengthValidator implements Validator
{
    private int $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength=$minLength;
    }

    /**
     * Validate a field against this Validators criteria
     * @param $field AbstractField to validate
     * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
     * @throws InvalidValueException if not valid
     */
    public function validate(AbstractField $field, NameableContainer $fields)
    {
        $len=strlen($field->getValue()??"");
        if($len>$this->minLength)
            throw new InvalidValueException('Minimum is '.$this->minLength.' characters');
    }
}