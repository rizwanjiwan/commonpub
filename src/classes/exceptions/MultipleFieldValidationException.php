<?php

namespace rizwanjiwan\common\classes\exceptions;

use Exception;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\FieldValidationErrors;

class MultipleFieldValidationException extends Exception
{

    private NameableContainer $errors;

    /**
     * @param NameableContainer $errors of FieldValidationErrors
     * @return void
     */
    public function setErrors(NameableContainer $errors):void
    {
        $this->errors=$errors;
    }

    /**
     * @return NameableContainer of FieldValidationErrors
     */
    public function getErrors():NameableContainer
    {
        return $this->errors;
    }
    public function getFirstError():string
    {
        foreach($this->getErrors() as $fieldValidationErrors) {/**@var $fieldValidationErrors FieldValidationErrors*/
            foreach($fieldValidationErrors->getErrors() as $err){
                return $err;
            }
        }
        return "";
    }
    public function getErrorsJson():string
    {
        $return=array();
        foreach($this->getErrors() as $fieldValidationErrors) {/**@var $fieldValidationErrors FieldValidationErrors*/
            $return[$fieldValidationErrors->getUniqueName()]=$fieldValidationErrors->getErrors();
        }
        return json_encode($return);
    }

    /**
     * Reverse out array of array (string errors).
     * @param mixed $errors
     * @return array
     */
    public static function fromJson(mixed $errors):array
    {
        return json_decode($errors,true);
    }

}