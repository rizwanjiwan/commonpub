<?php

namespace rizwanjiwan\common\web\helpers;

use rizwanjiwan\common\classes\exceptions\MultipleFieldValidationException;
use rizwanjiwan\common\web\ValidatableKeys;

/**
 * Used to provide information between edit and postEdit view when error occur.
 * Allows keeping the form state up to date with either default or what the user entered data
 */
class FormHelper
{
    /**
     * @var string[][] key=field key; value=string[] of errors
     */
    private ?array $errors=null;

    /**
     * @var array Default values key=>value
     */
    private array $defaults=array();

    public function __construct()
    {
        //grab errors
        if(array_key_exists(ValidatableKeys::VALIDATION_ERRORS,$_REQUEST)){
            $errors=MultipleFieldValidationException::fromJson($_REQUEST[ValidatableKeys::VALIDATION_ERRORS]);
            foreach($errors as $key=>$val){
                $this->errors[$key]=$val;
            }
        }
    }


    /**
     * Get any errors for a given field
     * @param string $key key of the field
     * @return string[] of errors. Might be length=0
     */
    public function getError(string $key):array
    {
        if($this->errors===null){
            return [];  //no errors
        }
        if(array_key_exists($key,$this->errors)){
            return $this->errors[$key];
        }
        return [];
    }

    /**
     * Manually specify a default value for a given key
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setDefault(string $key,string $value):self
    {
        $this->defaults[$key]=$value;
        return $this;
    }

    /**
     * Called by the view to get the value needed
     * @param string $key the key you want the value for
     * @return string the value
     */
    public function get(string $key):string
    {
        $defaultValue="";

        if(array_key_exists($key,$_REQUEST)){
            $defaultValue=$_REQUEST[$key];
        }
        elseif(array_key_exists($key,$this->defaults)){
            $defaultValue=$this->defaults[$key];;
        }
        return $this->getValue($key);
    }
    /**
     * Get the value to fill the form with for a specific field.
     * This will only be called if there is no override provided by the
     * instanciator of this class
     * Override this which, by default, returns an empty string
     * @param string $key the field key we want a value from
     * @return string the value to fill the $key form field with
     */
    protected function getValue(string $key): string
    {
        return "";
    }

}