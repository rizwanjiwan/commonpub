<?php

namespace rizwanjiwan\common\web;

use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\web\fields\AbstractField;

class FieldValidationErrors implements Nameable
{

    /**
     * @var AbstractField Key of the field which these errors apply
     */
    private AbstractField $field;
    /**
     * @var string[] the error messages
     */
    private array $errors;

    public function __construct(AbstractField $field, array $errors){
        $this->field=$field;
        $this->errors=$errors;
    }
    public function getFriendlyName(): string
    {
        return $this->field->getFriendlyName();
    }

    public function getUniqueName(): string
    {
        return $this->field->getUniqueName();
    }

    public function getField():AbstractField
    {
        return $this->field;
    }
    /**
     * @return string[] the errors
     */
    public function getErrors():array
    {
        return $this->errors;
    }
}