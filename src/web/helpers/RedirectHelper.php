<?php

namespace rizwanjiwan\common\web\helpers;

use rizwanjiwan\common\classes\exceptions\MultipleFieldValidationException;
use rizwanjiwan\common\web\Request;
use rizwanjiwan\common\web\ValidatableKeys;

/**
 * Makes it easier to redirect users with various things including:
 * - Validation errors
 * - Alert messages
 * - Random get parameters
 * - Passing through any get/post parameters from the current request
 */
class RedirectHelper
{
    /**
     * @var string Where do you want to redirect them to (URL with no get params)
     */
    private string $targetUrl;
    private Request $request;
    private ?string $alertType=null;
    private ?string $alertMessage=null;
    private ?string $alertDetails=null;
    /**
     * @var string[] key=value get params to add on to the redirect
     */
    private array $getParams=array();
    /**
     * @var ?MultipleFieldValidationException The validation errors
     */
    private ?MultipleFieldValidationException $validationErrors=null;

    /**
     * @param Request $request the web request
     * @param string $targetUrl the url to redirect to
     */
    public function __construct(Request $request, string $targetUrl)
    {
        $this->request=$request;
        $this->targetUrl=$targetUrl;
    }

    /**
     * Show an alert after redirect
     * @param string $alertType Alert::TYPE_*
     * @param string $message message to show
     * @param string|null $details details to show along with the message. Option (null=no details)
     * @return self
     */
    public function setAlertMessage(string $alertType, string $message, ?string $details=null):self
    {
        $this->alertType=$alertType;
        $this->alertMessage=$message;
        $this->alertDetails=$details;
        return $this;
    }

    public function setValidationErrors(MultipleFieldValidationException $errors):self
    {
        $this->validationErrors=$errors;
        return $this;
    }
    /**
     * Add get params in the redirect
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addGetParams(string $key, string $value):self
    {
        $this->getParams[$key]=$value;
        return $this;
    }

    /**
     * Specify any params from $_REQUEST to pass back through
     * @param $key string key into $_REQUEST
     * @return self
     */
    public function addPassThroughGetParams(string $key):self
    {
        if(array_key_exists($key,$_REQUEST)){
            $this->getParams[$key]=$_REQUEST[$key];
        }
        return $this;
    }
    /**
     * Do the redirect
     */
    public function redirect(): self
    {
        //set alert message
        if($this->alertMessage!==null){//there is a message to alert
            if($this->alertDetails!==null){//use the details the user provided
                new Alert($this->alertType,$this->alertMessage,$this->alertDetails);
            }
            elseif($this->validationErrors!==null){//instead use the first validation error as the details
                new Alert($this->alertType,$this->alertMessage,$this->validationErrors->getFirstError());
            }
            else{//no details
                new Alert($this->alertType,$this->alertMessage);
            }
        }
        //done alert

        //build URL for redirect
        $url=$this->targetUrl."?";
        foreach($this->getParams as $key=>$value){
            $url.=urlencode($key).'='.urlencode($value??"")."&";
        }
        if($this->validationErrors!==null){
            $url.=urlencode(ValidatableKeys::VALIDATION_ERRORS).'='.urlencode($this->validationErrors->getErrorsJson());
        }
        $this->request->respondRedirect($url);
        return $this;
    }

}