<?php

namespace rizwanjiwan\common\web\dto;

use rizwanjiwan\common\classes\exceptions\DtoException;
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\classes\LogManager;
use rizwanjiwan\common\interfaces\FieldGenerator;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\web\ValidatableKeys;
use rizwanjiwan\common\web\fields\TextField;

class ValidateQueryDto
{
    /**
     * @var FieldContainer All the fields with the current value in the form
     */
    public FieldContainer $fields;
    /**
     * @var AbstractField The field we're validating
     */
    public AbstractField $fieldToValidate;

    /**
     *
     * @param FieldGenerator $fieldGenerator A class that can convert the key-value pairs from the user into fields
     * @throws DtoException on input error
     */
    public function __construct(FieldGenerator $fieldGenerator)
    {
        $input=trim(file_get_contents('php://input'));
        $log=LogManager::createLogger('ValidateQueryDto');
        $log->debug("Input: ".$input);
        $json=json_decode($input,true);
        if($json===null){
            throw new DtoException('Invalid Json: '.$input);
        }
        if(array_key_exists(ValidatableKeys::FIELDS,$json)===false){
            throw new DtoException('Missing fields key');
        }
        if(array_key_exists(ValidatableKeys::FIELD_BEING_VALIDATED,$json)===false){
            throw new DtoException('Missing validation field key');
        }
        $this->fields=new FieldContainer();
        foreach($json[ValidatableKeys::FIELDS] as $key=>$val){ //go through each field and slap it into a field array
            $this->fields->add($fieldGenerator->toField($key,$val));
        }
        //get the field and the validators for this field
        $field=$this->fieldToValidate=$this->fields->get($json[ValidatableKeys::FIELD_BEING_VALIDATED]);
        if($field===null){
            throw new DtoException('Missing field to validate value');
        }
        $this->fieldToValidate=$this->fields->get($json[ValidatableKeys::FIELD_BEING_VALIDATED]);
    }


}