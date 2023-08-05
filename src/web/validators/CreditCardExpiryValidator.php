<?php

namespace rizwanjiwan\common\web\validators;

use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\classes\NameableContainer;
use rizwanjiwan\common\web\fields\AbstractField;

class CreditCardExpiryValidator implements Validator
{

    public function validate(AbstractField $field, NameableContainer $fields)
    {
        $val=$field->getValue();
        if(empty($val)){
            return;//don't bother checking empty
        }
        $parts=explode('/',$val);
        //do we have a month/year?
        if(count($parts)!==2){
            throw new InvalidValueException('Provide an expiry in the format MM/YY');
        }
        $month=intval($parts[0]);
        $year=intval($parts[1]);

        //month is valid?
        if (($month < 1) || ($month > 12)) {
            throw new InvalidValueException("Month must be between 01 and 12");
        }
        //year is valid?
        if (($year < intval(date('y'))) || ($year > intval(date('y'))+10)) {
            throw new InvalidValueException("Year is invalid");
        }
    }
}