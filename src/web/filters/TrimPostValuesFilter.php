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
            $_REQUEST[$key]=trim($val);
        }
    }
}