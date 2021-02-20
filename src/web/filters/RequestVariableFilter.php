<?php


namespace rizwanjiwan\common\web\filters;


use Exception;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\web\Filter;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\validators\Validator;

class RequestVariableFilter implements Filter
{

    use NameableTrait;

    /**
     * @var array of array of Validators to run where the key is the key into $_REQUEST for the value.
     */
    private array $requirements=array();
    /**
     * @var string[] key is the key into $_REQUEST and value is the error that was thrown from validating.
     */
    private array $errors=array();

    public static function create():self
    {
        return new self();
    }

    /**
     * Add a validation to run
     * @param $key string what to validate ($_REQUEST[$key])
     * @param null|Validator $validator How to validate or null to just confirm it's set. Validators based on other fields aren't supported at this time.
     */
    public function addValidation(string $key,?Validator $validator=null)
    {
        if(array_key_exists($key,$this->requirements)===false)
            $this->requirements[$key]=array();
        $keyReqs=$this->requirements[$key];

        if($validator!==null)
            array_push($keyReqs,$validator);

        $this->requirements[$key]=$keyReqs;
    }

    /**
     * This will run the validation but not exit. Use isError() etc. to learn about errors
     * @param Request $request
     */
    public function filter(Request $request)
    {
        //get all our fields into the nameable container
        $fields=NameableContainer::create();
        foreach($this->requirements as $key=>$validations)
        {
            if(array_key_exists($key,$_REQUEST)!==false)
                $fields->add(new RequestVariableField($key));
        }

        //now validate
        foreach($this->requirements as $key=>$validations)
        {
            if(array_key_exists($key,$_REQUEST)===false)
                $this->addError($key,'Value is missing');
            else
            {
                try//we only allow the first error per key
                {
                    foreach($validations as $validator)//run all the validations, log errors
                    {/**@var $validator Validator */
                        $field=$fields->get($key);
                        /**@var $field AbstractField*/
                        $validator->validate($field,$fields);
                    }
                }
                catch(Exception $e)
                {
                    $this->addError($key,$e->getMessage());
                }
            }
        }
    }

    private function addError(string $key,string $error)
    {
        $this->errors[$key]=$error;
    }

    public function isError():bool
    {
        return count($this->errors)>0;
    }

    /**
     * Get the errors that may have occured
     * @return string[] key= the key into $_REQUEST and value = error message
     */
    public function getErrors():array
    {
        return $this->errors;
    }

}