<?php

namespace rizwanjiwan\common\web\dto;

use rizwanjiwan\common\web\SearchKeys;

class ErrorResultDto implements DataTransferObject
{
    private string $error;

    public function __construct(string $error){
        $this->error=$error;
    }

    public function jsonSerialize(): mixed
    {
        return array(SearchKeys::ERROR=>$this->error);
    }
}