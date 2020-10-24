<?php
/**
 * For using RequestVariableFilter (internal, don't use it for anything else please).
 *
 * Pulls a value from $_REQUEST into an Abstract field
 */

namespace rizwanjiwan\common\web\filters;


use Exception;
use rizwanjiwan\common\web\fields\AbstractField;

class RequestVariableField extends AbstractField
{

    private $value;

    /**
     * RequestVariableField constructor.
     * @param $key string key into $_REQUEST
     */
    public function __construct($key)
    {
        parent::__construct($key, $key);
        $this->value=$_REQUEST[$key];
    }

    public function isValueArray()
    {
        return false;
    }

    public function setValue($value)
    {
        $this->value=$value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getValuePrintable()
    {
        return $this->getValue();
    }

    /**
     * @return bool|void
     * @throws Exception
     */
    public function isDefault()
    {
        throw new Exception('Not implemented');
    }
}