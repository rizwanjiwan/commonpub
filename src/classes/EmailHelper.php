<?php
/**
 * Helps with Email formatting
 */

namespace rizwanjiwan\common\classes;


use DateTime;
use DateTimeZone;
use PHPMailer\PHPMailer\Exception;
use rizwanjiwan\common\classes\exceptions\MailException;
use rizwanjiwan\common\classes\jobs\JobPoolProcessor;
use PHPMailer\PHPMailer\PHPMailer;
use rizwanjiwan\common\interfaces\FormatHelper;
use stdClass;

class EmailHelper implements FormatHelper
{
	const FORMAT_NORMAL=0;
	const FORMAT_HOST_ONLY=1;
	const FORMAT_USER_ONLY=2;

	private ?string $valueUnformatted=null;
	private ?string $errorReason=null;

	//key=>value for faster lookups
	private static array $invalidLogins=
		array(
			'pleaseask'=>1,
			'nomail'=>1,
			'none'=>1,
			'noemail'=>1,
			'noemailadress'=>1,
			'no'=>1,
			'na'=>1,
			'ask'=>1,
			'askme'=>1,
			'fake'=>1
		);
	private static array $invalidHosts=
		array(
			'askme.com'=>1,
			'noemail.com'=>1,
			'na.ca'=>1,
			'none.com'=>1,
			'none.ca'=>1,
			'ask.com'=>1,
			'fake.ca'=>1,
			'fake.com'=>1
		);
	private static array $invalidAddresses=
		array(
			'test@gmail.com'=>1
		);

	/**
	 * EmailHelper constructor.
	 * @param $value string|null the value to set
	 */
	public function __construct(?string $value=null)
	{
		$this->setValue($value);
	}

	/**
	 * Set a value to use in this formatter
	 * @param $value ?string the value to use
	 */
	public function setValue(?string $value)
	{
		$this->valueUnformatted=$value;
	}

	/**
	 * Check if the set value is valid for this format
	 * @return boolean true if the value is valid
	 */
	public function isValid():bool
	{
		$value=$this->getFormatted();
		if(filter_var($value, FILTER_VALIDATE_EMAIL)===false)
		{
			$this->errorReason='Invalid email format';
			return false;
		}
		//we tray to check for "fake" addresses with a few basic length checks and lookup in common bad login/host/email address tables
		$emailParts=explode('@',$value);
		$login=$emailParts[0];
		$host=$emailParts[1];
		if((strlen($login)<1)||
			(array_key_exists($login,self::$invalidLogins))||
			(array_key_exists($host,self::$invalidHosts))||
			(array_key_exists($value,self::$invalidAddresses)))
		{
			$this->errorReason="$value appears to be fake";
			return false;
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
	 * @param ?int $format_type The email format to use (see self::FORMAT_*)
	 * @return ?string the value formatted appropriately. Will do best effort if isValid() doesn't return true
	 */
	public function getFormatted(int $format_type=null):?string
	{
	    if(($format_type===null)||($this->valueUnformatted===null))
	        $format_type=self::FORMAT_NORMAL;
		$value=strtolower(trim($this->valueUnformatted));
		if($format_type===self::FORMAT_NORMAL)
			return $value;
		//they want host or user only
		$parts=explode('@',$value);
		if($format_type===self::FORMAT_HOST_ONLY)
			return $parts[1];	//host
		return $parts[0];	//user
	}

    /**
     * @param $from EmailPerson from address
     * @param $replyTo EmailPerson|null where replies should go. null if same as from
     * @param $to NameableContainer of EmailPerson - the addresses to send to
     * @param string $subject
     * @return PHPMailer
     * @throws Exception
     */
    private static function getMailer(EmailPerson $from,?EmailPerson $replyTo,NameableContainer $to,string $subject):PHPMailer
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        //$mail->SMTPDebug = 2;
        $mail->Host = Config::get('SMTP_HOST');
        $mail->Port = Config::get('SMTP_PORT');
        $mail->SMTPSecure = Config::get('SMTP_SECURE');
        $mail->SMTPAuth = true;
        $mail->Username = Config::get('SMTP_LOGIN');
        $mail->Password = Config::get('SMTP_PASSWORD');
        $mail->setFrom($from->getUniqueName(), $from->getFriendlyName());

        if($replyTo!==null)
            $mail->addReplyTo($replyTo->getUniqueName(),$replyTo->getFriendlyName());
        foreach($to as $add)
            $mail->addAddress($add->getUniqueName(),$add->getFriendlyName());
        $mail->Subject = $subject;
        return $mail;
    }

	/**
	 * Send an email right now
	 * @param $from EmailPerson from address
	 * @param $replyTo EmailPerson|null where replies should go. null if same as from
	 * @param $to NameableContainer of EmailPerson - the addresses to send to
	 * @param $subject string subject
	 * @param $body string html body
	 * @param $attachments EmailAttachment[] of files to send as attachments
	 * @throws MailException on error sending mail
	 */
	public static function sendMail(EmailPerson $from,?EmailPerson $replyTo,NameableContainer $to,string $subject,string $body,array $attachments=array())
	{
		if(strcmp(Config::get('ENV'),'prod')===0)
		{
			try
			{
                $mail=self::getMailer($from,$replyTo,$to,$subject);
                $mail->msgHTML($body);
				$mail->AltBody = 'This is an HTML email, please use a compatible email reader.';
				foreach($attachments as $attachment)
					$mail->addAttachment($attachment->path(),$attachment->fileName());

				if (!$mail->send())
					throw new MailException( "Mailer Error: " . $mail->ErrorInfo);
			}
			catch (Exception $e)
			{
				throw new MailException( "Mailer Error: " . $e->getMessage(),0,$e);
			}
		}
		else if(Config::getBool('SUPPRESS_DUMP_EMAIL')===false)
		{
			echo "FROM: ".$from->getFriendlyName()." <".$from->getUniqueName().">\n";
			echo "TO: ";
			foreach($to as $add) /**@var $add EmailPerson*/
				echo $add->getFriendlyName()." <".$add->getUniqueName().">,";
			echo "\n";
			echo "SUBJECT: ".$subject."\n\n";
			echo $body."\n\n";
		}
		//else do nothing
	}

	/**
	 * Validate that an object will work if you try to use to send email
	 * @param $obj stdClass
	 * @return bool true if it's good.
	 */
	private static function validateSendMailJob(stdClass $obj):bool
	{
		if($obj===null)
			return false;
		if($obj instanceof stdClass)
		{
			if(property_exists($obj,'from')===false)
				return false;
			if(property_exists($obj,'to')===false)
				return false;
			if(property_exists($obj,'replyTo')===false)
				return false;
			if(property_exists($obj,'subject')===false)
				return false;
			if(property_exists($obj,'body')===false)
				return false;
			return true;
		}
		return false;
	}

    /**
     * @param EmailPerson $from
     * @param EmailPerson|null $replyTo
     * @param NameableContainer $to
     * @param string $subject
     * @param DateTime $start
     * @param DateTime $end
     * @param string $body
     * @param string $location
     * @return void
     * @throws MailException
     */
    public static function sendCalendarInvite(EmailPerson $from,
                                              ?EmailPerson $replyTo,
                                              NameableContainer $to,
                                              string $subject,
                                              DateTime $start,
                                              DateTime $end,
                                              string $body,
                                              string $location)
    {
        if(strcmp(Config::get('ENV'),'prod')===0)
        {

            try
            {
                $mail=self::getMailer($from,$replyTo,$to,$subject);
                $mail->isHTML(false);
                $mail->ContentType = 'text/calendar';

                $mail->addCustomHeader('Content-type',"text/calendar; method=REQUEST; charset=UTF-8");
                $mail->addCustomHeader('Content-Transfer-Encoding',"7bit");
                $mail->addCustomHeader('X-Mailer',"Microsoft Office Outlook 12.0");
                $mail->addCustomHeader("Content-class: urn:content-classes:calendarmessage");
                $startString=$start->setTimezone(new DateTimeZone("UTC"))->format('Ymd\This').'Z';
                $endString=$end->setTimezone(new DateTimeZone("UTC"))->format('Ymd\This').'Z';

                $ical = 'BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
DTEND:' . $endString . '
UID:' . md5($startString.$body.$endString) . '
DTSTAMP:' . time() . '
LOCATION:' . addslashes($location) . '
DESCRIPTION:' . addslashes($body) . '
SUMMARY:' . addslashes($subject) . '
DTSTART:' . $startString . '
END:VEVENT
END:VCALENDAR';
                $mail->Body=$ical;


                if (!$mail->send())
                    throw new MailException( "Mailer Error: " . $mail->ErrorInfo);
            }
            catch (Exception $e)
            {
                throw new MailException( "Mailer Error: " . $e->getMessage(),0,$e);
            }
        }
        //else do nothing
    }
    /**
     * Send an email though the job pool
     * @param $pool string the job pool to use
     * @param $from EmailPerson from address
     * @param $replyTo EmailPerson|null where replies should go. null if same as from
     * @param $to NameableContainer of EmailPerson - the addresses to send to
     * @param $subject string subject
     * @param $body string html body
     * @param array|null $attachment null unused but placeholder for the future
     * @param null|int $priority The priority you want to assign to this job or null for default (1024)
     * @param null|int $delay the delay in seconds you want to assign to this job or null for default (0)
     */
	public static function createSendMailJob(string $pool,EmailPerson $from,?EmailPerson $replyTo,NameableContainer $to,string $subject,string $body,?array $attachment=null,?int $priority=null,?int $delay=null)	//todo: add attachment support
	{
		if(strcmp(Config::get('ENV'),'prod')===0)
		{
			//encode everything we need into a stdClass
			$obj = new stdClass();
			$obj->from = $from->stdClassEncode();
			if($replyTo!==null)
				$obj->replyTo=$replyTo->stdClassEncode();
			else
				$obj->replyTo=null;
			$obj->to=array();

			foreach($to as $add) /**@var $add EmailPerson*/
				array_push($obj->to,$add->stdClassEncode());
			$obj->subject = $subject;
			$obj->body=$body;
			JobPoolProcessor::addJob($pool,$obj,$priority,$delay);//set to send later
		}

	}

	/**
	 * Send the email that was previously saved as a job
	 * @param $obj stdClass
	 * @throws MailException
	 */
	public static function doSendMailJob(stdClass $obj)
	{
		if(strcmp(Config::get('ENV'),'prod')===0)
		{
			$log=LogManager::createLogger('EmailHelper');
			if(self::validateSendMailJob($obj)===false)
			{
				$log->error('Send mail job failed due to invalid object');
				return;
			}
			$tos=NameableContainer::create();
			foreach($obj->to as $add)
				$tos->add(EmailPerson::fromStdClass($add));

			self::sendMail(
				EmailPerson::fromStdClass($obj->from),
				EmailPerson::fromStdClass($obj->replyTo),
				$tos,
				$obj->subject,
				$obj->body
			);
			$log->info('Mail sent');
		}
	}
}