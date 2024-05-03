<?php

namespace rizwanjiwan\common\traits;

use Exception;
use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\exceptions\MultipleFieldValidationException;
use rizwanjiwan\common\classes\FieldContainer;
use rizwanjiwan\common\classes\LogManager;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\interfaces\FieldGenerator;
use rizwanjiwan\common\web\dto\ErrorResultDto;
use rizwanjiwan\common\web\dto\ValidateQueryDto;
use rizwanjiwan\common\web\dto\ValidateResultDto;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\web\FieldValidationErrors;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\ValidatableKeys;
use rizwanjiwan\common\web\validators\Validator;

trait ValidatableTrait
{

    /**
     * A method to call when validating something with a REST call
     * @param Request $request
     * @return void
     */
    public function validateRest(Request $request):void
    {
        try {
            $dto = new ValidateQueryDto($this->getFieldGenerator());
            $validators = $this->getValidator($dto->fieldToValidate);
            //validate
            $errors = new ValidateResultDto();
            $nameableContainerFields = $dto->fields->getNameableContainer();
            foreach ($validators as $validator) {
                try {
                    $validator->validate($dto->fieldToValidate, $nameableContainerFields);
                } catch (InvalidValueException $e) {
                    $errors->addError($e->getMessage());
                }
            }
            $request->respondJson($errors);//send back the response
        }
        catch(Exception $e){
            $log=LogManager::createLogger('ValidatableControllerTrait');
            $log->error($e->getMessage());
            $request->respondJson(new ErrorResultDto($e->getMessage()));
        }
    }

    /**
     * Validate input from a post by validating any fields that come in the $keyTo Validate
     * @param string[] $keysToValidate the keys into $_REQUEST to validate
     * @param bool $ignoreErrors true if you want to skip error checking
     * @return FieldContainer Returns if validation passes. Contains fields validated
     * @throws MultipleFieldValidationException If validation fails. Contains details.
     */
    public function validatePost(array $keysToValidate,bool $ignoreErrors=false):FieldContainer
    {
        $log=LogManager::createLogger('validate');
        //$log->debug('start');

        $fields=$this->getFieldsFromRequest($keysToValidate);
        if($ignoreErrors){
            return $fields;//done our work! Easy butty
        }
        //validate each field
        $allErrors=new NameableContainer();
        $nameableContainerFields=$fields->getNameableContainer();
        foreach($fields as $field){
            $validators=$this->getValidator($field);
            $thisFieldsErrors=array();
            foreach($validators as $validator){
                try{
                    $validator->validate($field,$nameableContainerFields);
                }catch(InvalidValueException $e){
                    array_push($thisFieldsErrors,$field->getFriendlyName().": ".$e->getMessage());
                }
            }
            //check if any validators failed. If they did, track them in allErrors
            if(count($thisFieldsErrors)>0){
                //$log->debug($field->getUniqueName());
                $allErrors->add(new FieldValidationErrors($field,$thisFieldsErrors));
            }
        }
        //we've gone through all fields and tried to validate, if there are any errors, throw them out as an exception
        if($allErrors->count()>0){
            $e=new MultipleFieldValidationException('Failed validation');
            $e->setErrors($allErrors);
            throw $e;
        }
        return $fields;
    }

    /**
     * Get values out of the $_REQUEST array into the appropriate AbstractFiled
     * @param string[] $keysToValidate
     * @return FieldContainer
     */
    public function getFieldsFromRequest(array $keysToValidate):FieldContainer
    {
        $fields=new FieldContainer();
        $generator=$this->getFieldGenerator();
        foreach($keysToValidate as $key){ //go through each field and slap it into a field array
            if(array_key_exists($key,$_REQUEST)){
                $fields->add($generator->toField($key,$_REQUEST[$key]));
            }
            else{
                $fields->add($generator->toField($key,null));
            }
        }
        return $fields;
    }

    /**
     * Converts a $key into a plausable friendly name
     * @param string $key
     * @return string
     */
    protected function calculateFriendlyName(string $key):string
    {
        $words=explode("_",$key);
        $retString="";
        foreach($words as $word){
            $retString.=ucfirst($word)." ";
        }
        return trim($retString);
    }

    /**
     * Get the validators for a given field
     * @param AbstractField $field
     * @return Validator[]
     */
    protected abstract function getValidator(AbstractField $field): array;

    /**
     * Get the field generator to use to convert input from the user to fields
     * @return FieldGenerator
     */
    protected abstract function getFieldGenerator(): FieldGenerator;
}