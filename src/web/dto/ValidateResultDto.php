<?php

namespace rizwanjiwan\common\web\dto;

class ValidateResultDto implements DataTransferObject
{

    private array $errors=array();

    public function addError(string $error):void
    {
        array_push($this->errors,$error);
    }

    public function jsonSerialize(): array
    {
        return $this->errors;
    }
}