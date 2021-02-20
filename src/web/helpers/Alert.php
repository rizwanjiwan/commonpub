<?php
/**
 * Encapsulates Alerting from controller to view
 */
namespace rizwanjiwan\common\web\helpers;

class Alert
{
    const TYPE_SUCCESS='success';
    const TYPE_INFO='info';
    const TYPE_WARNING='warning';
    const TYPE_DANGER='danger';

    const SESSION_ALERT_TYPE='alert.type';
    const SESSION_ALERT_MESSAGE='alert.message';
    const SESSION_CLOBBER_IS_ALERT='alert.clobber';

    private ?string $type=self::TYPE_SUCCESS;
    private ?string $message="";
    private bool $canClobber=false;

    /**
     * Alert constructor. Pass values to set the alerts, otherwise it will use sessions to pull up old alerts.
     * @param $type string|null TYPE_* variable
     * @param $message string|null the message to display to the user
     * @param $canClobber true to allow clobbering of this specific alert. This allows you to use Alerts for messages that can/can't be clobbered
     */
    public function __construct(?string $type=null,?string $message=null,bool $canClobber=false)
    {
        if(!isset($_SESSION))
            session_start();
        if($type===null)//load from session variable
        {
            $this->type=null;
            $this->message=null;
            if(isset($_SESSION[self::SESSION_ALERT_TYPE]))
                $this->type=$_SESSION[self::SESSION_ALERT_TYPE];
            if(isset($_SESSION[self::SESSION_ALERT_MESSAGE]))
                $this->message=$_SESSION[self::SESSION_ALERT_MESSAGE];
            if(isset($_SESSION[self::SESSION_CLOBBER_IS_ALERT]))
                $this->canClobber=$_SESSION[self::SESSION_CLOBBER_IS_ALERT];
        }
        else//update anything that might be in the cookies since we just clobbered that data
        {
            $this->type=$type;
            $this->message=$message;
            $this->canClobber=$canClobber;
            $_SESSION[self::SESSION_ALERT_TYPE]=$this->type;
            $_SESSION[self::SESSION_ALERT_MESSAGE]=$this->message;
            $_SESSION[self::SESSION_CLOBBER_IS_ALERT]=$this->canClobber;
        }

    }
    public function isAlert():bool
    {
        return $this->type!==null;
    }

    /**
     * @return bool true if there is no alert or the alert can be clobbered
     */
    public function canClobber():bool
    {
        return ($this->isAlert()===false)||$this->canClobber;
    }

    /**
     * @return null|string null if there isn't any alert. String is TYPE_*
     */
    public function getType():?string //clear out session variables since we're consuming the alerts.
    {
        unset($_SESSION[self::SESSION_ALERT_TYPE]);
        unset($_SESSION[self::SESSION_ALERT_MESSAGE]);
        return $this->type;
    }

    /**
     * @return null|string null if there isn't any alert.
     */
    public function getMessage():?string//clear out session variables since we're consuming the alerts.
    {
        unset($_SESSION[self::SESSION_ALERT_TYPE]);
        unset($_SESSION[self::SESSION_ALERT_MESSAGE]);
        return $this->message;
    }
}