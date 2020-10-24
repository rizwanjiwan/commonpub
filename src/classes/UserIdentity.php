<?php


namespace rizwanjiwan\common\classes;


use rizwanjiwan\common\classes\exceptions\AuthorizationException;
use Exception;
use rizwanjiwan\common\traits\SingeltonTrait;

class UserIdentity
{
    use SingeltonTrait;

    const METHOD_GOOGLE=1;
    const METHOD_AZURE_AD=2;
    const METHOD_DEV_MODE=3;

    private $name=null;
    private $domain=null;
    private $email=null;
    private $picture=null;
    private $secret=null;
    private $isAuthed=false;
    private $method=null;
    private $log;

    /**
     * UserIdentity constructor.
     */
    protected function __construct()
    {
        if(!isset($_SESSION))
        {
            try#avoid bad cookie values: https://stackoverflow.com/questions/32898857/session-start-issues-regarding-illegal-characters-empty-session-id-and-failed
            {
                session_start();
            }catch(Exception $e)
            {
                session_regenerate_id();
                session_start();//if it fails again, we're SOL...
            }
        }

        $this->log=LogManager::createLogger('Common');

        $this->secret=Config::get('AUTH_SECRET');
        //check if there is a session already set, validate it if it is
        if((array_key_exists('name',$_SESSION))&&
            (array_key_exists('domain',$_SESSION))&&
            (array_key_exists('email',$_SESSION))&&
            (array_key_exists('picture',$_SESSION))&&
            (array_key_exists('expiry',$_SESSION))&&
            (array_key_exists('method',$_SESSION))&&
            (array_key_exists('token',$_SESSION)))
        {
            $this->log->debug('Found session variables for user ');
            $validToken=$this->validateToken($_SESSION['token'],$_SESSION['name'],$_SESSION['domain'],$_SESSION['email'],$_SESSION['expiry']);
            if((!$validToken)||
                ($_SESSION['expiry']<time()))
            {
                $this->log->debug('Invalid token or expired');
                $this->clearIdentity();	//invalid or expired

            }
            else
            {	//valid, load from session
                $this->name=$_SESSION['name'];
                $this->domain=$_SESSION['domain'];
                $this->email=$_SESSION['email'];
                $this->picture=$_SESSION['picture'];
                $this->method=$_SESSION['method'];
                $this->isAuthed=true;
                $this->log->info('User logged in by session:'.$this->email);
            }
        }
        elseif((strcmp(Config::get('ENV'),'dev')===0)&&(Config::getBool('LOGIN_BYPASS')===true))
        {//support dev mode
            $this->log->debug('Dev mode ');
            try
            {
                $this->setIdentity('Developer Doe', 'doe.ca', 'developer@doe.ca', null, self::METHOD_DEV_MODE);
            }
            catch(AuthorizationException $e)
            {
                $this->clearIdentity();//clear everything just in case
            }
        }
        else
        {
            $this->clearIdentity();//clear everything just in case
        }
    }

    /**
     * Set the identity for a user
     * @param $name string name of the user
     * @param $domain string domain name for the user
     * @param $email string email of the user
     * @param $picture string url to the user's picture
     * @param $method int the METHOD_* of managing identity
     * @throws AuthorizationException if there is an issue due to the user's access
     */
    public function setIdentity($name,$domain, $email,$picture,$method)
    {
        $this->log->info('Logged in by setIdentity: '.$email);
        //make sure they're from an allowed domain
        $domain=strtolower($domain);
        $email=strtolower($email);
        $name=ucwords(strtolower($name));
        $specificUsers=Config::getArray('AUTH_ALLOWED_USERS');
        $authDomains=Config::getArray('AUTH_ALLOWED_DOMAINS');


        if(($specificUsers!==false)&&(array_search($email,$specificUsers)===false))//need to validate for specific users as well
            throw new AuthorizationException('Not Authorized user: '.$email);
        else if($specificUsers===false)//they didn't limit to specific users
        {
            if($authDomains!==false)//they provided a domain list
            {
                if(array_search($domain,$authDomains)===false)//check if the user's domain in the domian list
                    throw new AuthorizationException('Not Authorized domain: '.$email);
            }//otherwise, all domains are authorized
        }


        //all is good
        $this->isAuthed=true;
        $this->name=$name;
        $this->domain=$domain;
        $this->email=$email;
        $this->picture=$picture;
        $this->method=$method;
        $_SESSION['name']=$name;
        $_SESSION['domain']=$domain;
        $_SESSION['email']=$email;
        $_SESSION['picture']=$picture;
        $_SESSION['method']=$method;
        $_SESSION['expiry']=time()+(60*60*12);		//max duration
        $_SESSION['token']=$this->generateToken($name,$domain,$email,$_SESSION['expiry']);
    }

    /**
     * Clear the previously set identity
     */
    public function clearIdentity()
    {
        $this->log->debug('clearing identity');
        $this->isAuthed=false;
        $this->name=null;
        $this->domain=null;
        $this->email=null;
        $this->picture=null;
        unset($_SESSION['name']);
        unset($_SESSION['domain']);
        unset($_SESSION['email']);
        unset($_SESSION['picture']);
        unset($_SESSION['expiry']);
        unset($_SESSION['token']);
        unset($_SESSION['method']);
    }

    /**
     * Take any number of string parameters and generates a secure token which can later be used to validate against the same parameters in the future
     * @param string[] ...$params order matters
     * @return string the token
     */
    private function generateToken(...$params)
    {
        $string='';
        foreach($params as $param)
        {
            $string.=$param;
        }
        $string.=$this->secret;	//add something that the outside world doesn't have
        return password_hash($string,PASSWORD_BCRYPT);
    }
    /**
     * Validate a previously generated token
     * @param string $token the token to validate
     * @param string[] ...$params as sent to genreateToken. Order matters
     * @return boolean true if it's valid
     */
    private function validateToken($token,...$params)
    {
        $string='';
        foreach($params as $param)
        {
            $string.=$param;
        }
        $string.=$this->secret;	//add something that the outside world doesn't have
        return password_verify($string,$token);
    }

    public function isAuthed()
    {
        return $this->isAuthed;
    }
    public function isDeveloper()
    {
        return strcmp($this->getDomain(),'jiwan.ca')===0;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDomain()
    {
        return $this->domain;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function getPicture()
    {
        return $this->picture;
    }
    public function getMethod()
    {
        if($this->method!==null)
            return $this->method;
        return '';
    }

    /**
     * Get the method as a user friendly name
     */
    public function getMethodName()
    {
        if($this->method===null)
            return 'Not logged in.';
        if($this->method===self::METHOD_GOOGLE)
            return 'Google';
        if($this->method===self::METHOD_AZURE_AD)
            return 'Office 365';
        if($this->method===self::METHOD_DEV_MODE)
            return 'DevMode';
        return 'Unknown';
    }


}