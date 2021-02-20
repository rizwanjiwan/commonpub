<?php
/**
 * Encapsulates information about an email address/person.
 */
namespace rizwanjiwan\common\classes;


use rizwanjiwan\common\classes\exceptions\MailException;
use rizwanjiwan\common\classes\exceptions\NameableException;
use rizwanjiwan\common\interfaces\Nameable;
use rizwanjiwan\common\traits\NameableTrait;
use stdClass;

class EmailPerson implements Nameable
{
	use NameableTrait;

	/**
	 * EmailPerson constructor.
	 * @param $email string
	 * @param $friendlyName string|null
	 * @throws MailException if the email address is invalid
	 */
	public function __construct(string $email, ?string $friendlyName=null)
	{
		$helper=new EmailHelper($email);
		if($helper->isValid()===false)
			throw new MailException('Invalid address: '.$helper->getInvalidFormatReason());
		try
		{
			$this->setUniqueName($email);
		}
		catch(NameableException $e)
		{
			//shouldn't be possible if it's a valid email
			throw new MailException('Invalid address: '.$email,0,$e);
		}
		$this->setFriendlyName($friendlyName===null?'':$friendlyName);
	}

	/**
	 * Nice method that makes chaining easier
	 * @param $email string
	 * @param null|string $friendlyName
	 * @return EmailPerson|null if the email address is invalid
	 */
	public static function create(string $email,?string $friendlyName=null):?EmailPerson
	{
		try
		{
			return new self($email,$friendlyName);

		}catch(MailException)
		{
			return null;
		}
	}
	/**
	 * Get a representation of this as a standard class
	 * @return stdClass
	 */
	public function stdClassEncode():stdClass
	{
		$obj=new stdClass();
		$obj->uniqueName=$this->getUniqueName();
		$obj->friendlyName=$this->getFriendlyName();
		return $obj;
	}

	/**
	 * Convert a representation from a stdClass back to this
	 * @param $obj stdClass
	 * @return EmailPerson|null null if object is null
	 * @throws MailException
	 */
	public static function fromStdClass(stdClass $obj):?EmailPerson
	{
		if($obj===null)
			return null;
		return new self($obj->uniqueName,$obj->friendlyName);

	}
}