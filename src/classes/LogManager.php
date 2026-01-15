<?php
/**
 * Abstracts away some underlying logger setup stuff etc.
 */

namespace rizwanjiwan\common\classes;

use PDO;
use rizwanjiwan\common\classes\monolog\HtmlRowFormatter;
use rizwanjiwan\common\classes\monolog\LoggingBufferHandler;
use rizwanjiwan\common\classes\monolog\LoggingToggleHandler;
use rizwanjiwan\common\classes\monolog\MySqlHandler;
use rizwanjiwan\common\classes\monolog\StdOutHandler;
use Exception;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;

class LogManager
{


    /**
     * @var null|Logger
     */
    private static ?Logger $baseLogger=null;
    private static bool $loggingOn=true;

    private static bool $htmlLogging=false;
    private static bool $consoleLogging=false;


    public static function htmlLogingOn()
    {
        self::$htmlLogging=true;
    }
    public static function htmlLoggingOff()
    {
        self::$htmlLogging=false;
    }

    public static function consoleLogingOn()
    {
        self::$consoleLogging=true;
    }
    public static function consoleLoggingOff()
    {
        self::$consoleLogging=false;
    }
    /**
     * Create a logger
     * @param null|string $name provide a name if you want to be able to have more robust sectioning in logging
     * @return Logger the logger you can use all nice and ready to go
     */
    public static function createLogger($name=null):Logger
    {
        if(self::$baseLogger===null)
        {
            //self::debug('creating base logger');
            try
            {
                //create the base
                if(Config::get('DB_LOG_TABLE'))
                    self::createDbLogger();
                else
                    self::createFileLogger();
            }catch(Exception $e)
            {
                //self::debug('Exception '.$e->getMessage());
                self::createFileLogger();
                self::$baseLogger->critical('DB Logger failed: '.$e->getMessage());
            }

        }
        if($name===null)//just base loger
            return self::$baseLogger;
        return self::$baseLogger->withName($name);
    }

    private static function createFileLogger()
    {
        //self::debug('Creating file logger.');
        self::$baseLogger = new Logger('Common');
        //set up a buffered html and rotating file handler as our defaults for every case
        $logLev=self::getLogLevel();
        if(self::$htmlLogging)
        {
            $buff=new LoggingBufferHandler($logLev);
            $buff->setFormatter(new HtmlRowFormatter());
            self::$baseLogger->pushHandler($buff);
        }
        if(self::$consoleLogging)
        {
            $buff=new StdOutHandler($logLev);
            self::$baseLogger->pushHandler($buff);
        }
        $rfh=new RotatingFileHandler(realpath(dirname(__FILE__)).Config::get('LOG_FILE'),14,$logLev,true,0666);
        $rfh->setFormatter(new LineFormatter("[%datetime%][%extra.uid%][%extra.class%::%extra.function%(%extra.line%)] %channel%.%level_name%: %message%\n",null,false,true));
        self::$baseLogger->pushHandler($rfh);
        //support toggling off/on overall
        self::$baseLogger->pushHandler(new LoggingToggleHandler());
        //tag additional information onto the output
        self::$baseLogger->pushProcessor(new UidProcessor());
        self::$baseLogger->pushProcessor(new IntrospectionProcessor());
    }

    private static function createDbLogger()
    {
        //self::debug('creating db logger');
        self::$baseLogger = new Logger('Common');
        //set up a buffered html and rotating file handler as our defaults for every case
        $logLev=self::getLogLevel();
        if(self::$htmlLogging)
        {
            //self::debug('creating html bugger logger');
            $buff=new LoggingBufferHandler($logLev);
            $buff->setFormatter(new HtmlRowFormatter());
            self::$baseLogger->pushHandler($buff);
        }
        if(self::$consoleLogging)
        {
            //self::debug('creating console logger');
            $buff=new StdOutHandler($logLev);
            self::$baseLogger->pushHandler($buff);
        }
        $pdo=self::getPdoConn();
        //self::debug('creating mysql handler');
        $msh=new MySQLHandler($pdo,Config::get('DB_LOG_TABLE'),array('uid','class','function','line'),$logLev);
        self::$baseLogger->pushHandler($msh);
        //support toggling off/on overall
        self::$baseLogger->pushHandler(new LoggingToggleHandler());
        //tag additional information onto the output
        self::$baseLogger->pushProcessor(new UidProcessor());
        self::$baseLogger->pushProcessor(new IntrospectionProcessor());
    }
    /**
     * Get the log output level we should run at
     * @return int 100 to 600.
     */
    private static function getLogLevel()
    {
        $logLevString=strtolower(Config::get('LOG_LEV'));
        if(strcmp($logLevString,'debug')===0)
            return Logger::DEBUG;
        if(strcmp($logLevString,'info')===0)
            return Logger::INFO;
        if(strcmp($logLevString,'notice')===0)
            return Logger::NOTICE;
        if(strcmp($logLevString,'warning')===0)
            return Logger::WARNING;
        if(strcmp($logLevString,'error')===0)
            return Logger::ERROR;
        if(strcmp($logLevString,'critical')===0)
            return Logger::CRITICAL;
        if(strcmp($logLevString,'alert')===0)
            return Logger::ALERT;
        if(strcmp($logLevString,'emergency')===0)
            return Logger::EMERGENCY;
        return Logger::NOTICE;
    }
    /**
     * Turn logging on (default is on)
     */
    public static function setLoggingOn()
    {
        self::$loggingOn=true;
    }
    /**
     * Turn logging off (default is on)
     */
    public static function setLoggingOff()
    {
        self::$loggingOn=false;
    }

    public static function isLoggingOn()
    {
        return self::$loggingOn;
    }

    public static function getHtmlLog()
    {
        return "<table>".LoggingBufferHandler::getBuffer()."</table>";
    }

    /**
     * @return PDO connection to logging db.
     */
    private static function getPdoConn()
    {
        $pdo=new PDO(
            'mysql:host='.Config::get('DB_LOG_HOST').';port='.Config::get('DB_LOG_PORT').';dbname='.Config::get('DB_LOG_DATABASE'),
            Config::get('DB_LOG_LOGIN'),
            Config::get('DB_LOG_PASSWORD'),
            array (
                'charset' => 'utf8mb4',
                'queries' =>
                    array (
                        'utf8'=>"SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, COLLATION_CONNECTION = utf8mb4_unicode_ci, COLLATION_DATABASE = utf8mb4_unicode_ci, COLLATION_SERVER = utf8mb4_unicode_ci"
                    ),
            ));
        return $pdo;
    }

    /**
     * Truncate the log if using db logging
     * @param int $daysAgo number of days back to save logs
     */
    public static function truncateLog($daysAgo=30)
    {
        if(Config::get('DB_LOG_TABLE'))//nothing to truncate if it's not a db log
        {
            //failsafe at 7 days
            $daysAgo=max(7,$daysAgo);

            $pdo=self::getPdoConn();
            $pdo->exec(
                'delete from `'.Config::get('DB_LOG_TABLE').'` where `time`<\''.date('Y-m-d',strtotime("-$daysAgo days")).'\''
            );
        }
    }

    public static function debug($string)
    {
        //nothing
    }

}
