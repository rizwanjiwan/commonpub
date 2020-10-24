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
	public function __construct($email, $friendlyName=null)
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
	public static function create($email,$friendlyName=null)
	{
		try
		{
			return new self($email,$friendlyName);

		}catch(MailException $e)
		{
			return null;
		}
	}
	/**
	 * Get a representation of this as a standard class
	 * @return stdClass
	 */
	public function stdClassEncode()
	{
		$obj=new stdClass();
		$obj->uniqueName=$this->getUniqueName();
		$obj->friendlyName=$this->getFriendlyName();
		return $obj;
	}

	/**
	 * Convert a representation from a stdClass back to this
	 * @param $obj stdClass
	 * @return EmailPerson
	 * @throws MailException
	 */
	public static function fromStdClass($obj)
	{
		if($obj===null)
			return null;
		return new self($obj->uniqueName,$obj->friendlyName);

	}
}