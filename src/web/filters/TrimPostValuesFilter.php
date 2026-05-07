<?php

namespace rizwanjiwan\common\web\filters;

use rizwanjiwan\common\traits\NameableTrait;
use rizwanjiwan\common\web\Filter;
use rizwanjiwan\common\web\Request;

class TrimPostValuesFilter implements Filter
{
    use NameableTrait;

    public function filter(Request $request)
    {
        foreach($_REQUEST as $key=>$val){
            $_REQUEST[$key]=$this->trimValue($val);
        }
    }

    private function trimValue(mixed $value):mixed
    {
        if(is_array($value)){
            return array_map(array($this,'trimValue'),$value);
        }
        if(is_string($value)){
            return trim($value);
        }
        return $value;
    }
}
