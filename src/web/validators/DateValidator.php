<?php
/**
 * Confirm that a value is a valid datetime
 */

namespace rizwanjiwan\common\web\validators;



use Exception;
use rizwanjiwan\common\classes\exceptions\InvalidValueException;
use rizwanjiwan\common\web\fields\AbstractField;
use rizwanjiwan\common\classes\NameableContainer;
use DateTime;

class DateValidator implements Validator
{


    /**
     * Validate a field against this Validators criteria
     * @param $field AbstractField to validate
     * @param $fields NameableContainer of AbstractField for the other fields values that might be needed in validation
     * @throws InvalidValueException if not valid
     */
    public function validate(AbstractField $field,NameableContainer $fields)
    {
        $value=$field->getValue();
        if(strlen($value??"")===0)
            return;//nothing to check
        self::isValid($value);
    }

    /**
     * Validate any old date string
     * @param string $dateString
     * @return DateTime valid date
     * @throws InvalidValueException if the string can't be interpreted as a date (or is empty/null)
     */
    public static function isValid(string $dateString):DateTime
    {
        if(strlen($dateString)===0)
            throw new InvalidValueException('Invalid Date: '.$dateString);
        try
        {
            if(is_int($dateString))
                return new DateTime(strtotime('Y-m-d H:i',$dateString));
            return new DateTime($dateString);//all is good
        }
        catch (Exception) //invalid format
        {
            throw new InvalidValueException('Invalid Date: '.$dateString);
        }
    }
}