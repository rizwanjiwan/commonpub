<?php
/**
 * Formatting for numbers
 */

namespace rizwanjiwan\common\classes;


use libphonenumber\PhoneNumber;
use rizwanjiwan\common\interfaces\FormatHelper;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class PhoneHelper implements FormatHelper
{

	const FORMAT_HUMAN=0;
	const FORMAT_E164=1;
	const FORMAT_LOCAL=2;
	const FORMAT_NUMBERS_ONLY=3;
	const BAD_FIRST_DIGITS=array(0,1);

	/**
	 * @var null|string
	 */
	private ?string $number=null;
	/**
	 * @var \libphonenumber\PhoneNumber|null
	 */
	private ?PhoneNumber $parsedNumber=null;

	/**
	 * @var null|string
	 */
	private ?string $errorReason=null;
	/**
	 * @var bool
	 */
	private bool $isValidCalled=false;

	/**
	 * PhoneNumberHelper constructor.
	 * @param $number ?string the number you want to work with
	 */
	public function __construct(?string $number=null)
	{
		$this->setValue($number);
	}


	/**
	 * Set a value to use in this formatter
	 * @param $number string
	 */
	public function setValue(string $number)
	{
		$this->number=$number;
		$this->isValidCalled=false;
	}

	/**
	 * Check if the set value is valid for this format
	 * @return boolean true if the value is valid
	 */
	public function isValid():bool
	{
		$this->isValidCalled=true;
		$this->errorReason=null;//clear error
		$this->parsedNumber=null;//clear stored parsed number
		if(($this->number===null)||(strlen($this->number)<7))
		{
			$this->errorReason='Invalid number';
			return false;
		}
		$phoneUtil = PhoneNumberUtil::getInstance();
		try
		{
			$this->parsedNumber=$phoneUtil->parse($this->number, "CA");
		} catch (NumberParseException $e)
		{
			$this->errorReason=$e->getMessage();
			return false;
		}
        $localNumber=$this->parsedNumber->getNationalNumber();//check for bad characters in traditional dialing.
        if(strlen($localNumber)>10)
        {
            $this->errorReason='Too long';
            return false;
        }
        if(strlen($localNumber)<10)
        {
            $this->errorReason='Too short';
            return false;
        }
        foreach(self::BAD_FIRST_DIGITS as $badDigit)
        {
            if((strcmp($localNumber[0],$badDigit)===0)||(strcmp($localNumber[3],$badDigit)===0))
            {
                $this->errorReason=$badDigit.' at start of number or area code';
                return false;
            }
        }
        return true;
	}

	/**
	 * @return string|null human friendly reason why the format is invalid. Null if it is valid or you never checked.
	 */
	public function getInvalidFormatReason():?string
	{
		return $this->errorReason;
	}

	/**
	 * @param null|int $format_type null will default to human format. int will be one of the FORMAT_* constants in this class
	 * @return string the value formatted appropriately. Will do best effort if isValid() doesn't return true
	 */
	public function getFormatted(?int $format_type=null):string
	{
		if($this->isValidCalled==false)
			$this->isValid();
		if($this->parsedNumber===null)//couldn't get it
			return $this->number;
		if($format_type!==null)//non-default formats
		{
			if($format_type===self::FORMAT_E164)
				return "+".$this->parsedNumber->getCountryCode().$this->parsedNumber->getNationalNumber();
			else if($format_type===self::FORMAT_LOCAL)
				return $this->parsedNumber->getNationalNumber();
			else if($format_type===self::FORMAT_NUMBERS_ONLY)
				return preg_replace("/[^0-9,.]/", "", $this->getHumanFormatted());
		}
		//human format
		return $this->getHumanFormatted();
	}

	/**
	 * @return string the parsed number in human format
	 */
	private function getHumanFormatted():string
	{
		$localNumber=$this->parsedNumber->getNationalNumber();
		$areacode=substr($localNumber,0,3);
		$firstPart=substr($localNumber,3,3);
		$secondPart=substr($localNumber,6);
		return "+1 (".$areacode.") ".$firstPart."-".$secondPart;
	}
}