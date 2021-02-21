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

    private string|array $value;

    /**
     * RequestVariableField constructor.
     * @param $key string key into $_REQUEST
     */
    public function __construct(string $key)
    {
        parent::__construct($key, $key);
        $this->value=$_REQUEST[$key];
    }

    public function isValueArray():bool
    {
        return false;
    }

    public function setValue(mixed $value):self
    {
        $this->value=$value;
        return $this;
    }

    public function getValue():string|array
    {
        return $this->value;
    }

    public function getValuePrintable():string|array
    {
        return $this->getValue();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isDefault():bool
    {
        throw new Exception('Not implemented');
    }
}