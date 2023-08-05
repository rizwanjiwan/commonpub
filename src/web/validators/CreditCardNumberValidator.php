<?php

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class CreditCardNumberValidator implements Validator
{

    public function validate(AbstractField $field, NameableContainer $fields)
    {
        $number=$field->getValue();
        if(empty($number)){
            return;//don't bother checking empty
        }
        //remove everything but numbers
        $number=preg_replace('/[^0-9]/', '', $number);
        //from https://gist.github.com/troelskn/1287893
        $sum = 0;
        $revNumber = strrev($number);
        $len = strlen($number);

        for ($i = 0; $i < $len; $i++) {
            $sum += $i & 1 ? ($revNumber[$i] > 4 ? $revNumber[$i] * 2 - 9 : $revNumber[$i] * 2) : $revNumber[$i];
        }

        $isValid=$sum % 10 === 0;
        if($isValid===false){
            throw new InvalidValueException('Invalid credit card number');
        }

    }
}