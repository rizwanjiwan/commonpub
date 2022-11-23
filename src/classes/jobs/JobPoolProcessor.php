<?php
/**
 * A thread to process a single pool of jobs
 */

namespace rizwanjiwan\common\classes\jobs;




//use Thread;

use Exception;
use Monolog\Logger;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use rizwanjiwan\common\classes\LogManager;
use stdClass;

class JobPoolProcessor
{
	const RETRY_PROPERTY='retry';

	private string $pool;
	private string $className;
	private int $retryLimit;
	private int $retryDelay;
	private static ?Logger $log=null;

	/**
	 * JobPoolProcessor constructor.
	 * @param $pool string the name of the pool to process
	 * @param $className string class name to process this job
	 * @param int $retryLimit int the number of times to DELAYED retry a failed job
	 * @param int $retryDelay int the number of minutes to delay a retry
	 */
	public function __construct(string $pool,string $className,int $retryLimit=0,int $retryDelay=60)
	{
		$this->pool=$pool;
		$this->className=$className;
		$this->retryLimit=$retryLimit;
		$this->retryDelay=$retryDelay;
		if(self::$log===null)
			self::$log=LogManager::createLogger('JobPoolProcessor');
	}

    /**
     * @throws Exception on connection error
     */
	public function run()
	{
		$queue =  Pheanstalk::create('127.0.0.1');
		$queue->watch($this->pool);
		while(true)
		{
            $job = $queue->reserveWithTimeout(50);
            if(isset($job))
            {
                $raw=$job->getData();
                self::$log->info('Processing '.$this->pool.'.'.$job->getId().': '.$raw);
                $decoded=null;
                try
                {
                    $decoded = json_decode($raw);
                    if(!$this->className)
                    {
                        $queue->bury($job);
                        self::$log->error('No job processor defined for '.$this->pool);
                        return;//die
                    }
                    $jobProcessor=new $this->className();
                    if(($jobProcessor instanceof JobProcessorInterface)===false)
                    {
                        $queue->bury($job);
                        self::$log->error('Job processor '.$this->className.' is invalid for pool '.$this->pool);
                        return;//die
                    }
                    $jobProcessor->process($decoded);
                    $queue->delete($job);//done job
                    self::$log->info('Done '.$this->pool.'.'.$job->getId());
                }
                catch(Exception $e)
                {
                    $retryProperty=self::RETRY_PROPERTY;
                    if(($decoded!==null)&&(property_exists($decoded,$retryProperty))&&($decoded->$retryProperty<$this->retryLimit))
                    {
                        //delay the job to retry
                        self::$log->info('[Scheduling Retry] Failed job ('.$this->pool. ' id='.$job->getId().') '.$e->getMessage().': '.$raw);
                        $queue->delete($job);//clear job
                        $decoded->$retryProperty=$decoded->$retryProperty+1;
                        self::addJob($this->pool,$decoded,null,$this->retryDelay*60);//add it back with delay
                    }
                    else
                    {
                        $queue->bury($job);
                        self::$log->error('Failed job ('.$this->pool. ' id='.$job->getId().') '.$e->getMessage().': '.$raw);
                        return;//die
                    }
                }
            }
		}
	}

	/**
	 * Add a job to later process
	 * @param $pool string the name of the pool to add this job to
	 * @param $data stdClass to send to the job. The data may not have any of the properties listed as constants on this class (e.g. retry).
	 * @param null|int $priority The priority you want to assign to this job or null for default (1024)
	 * @param null|int $delay the delay in seconds you want to assign to this job or null for default (0)
	 * @param null|int $ttr the max time to run in seconds you want to assign for this job or null for default (60)
	 */
	public static function addJob(string $pool,stdClass $data,$priority=null,$delay=null,$ttr=null)
	{
		if(self::$log===null)
			self::$log=LogManager::createLogger('JobPoolProcessor');
		if($priority===null)
			$priority= PheanstalkInterface::DEFAULT_PRIORITY;
		if($delay===null)
			$delay= PheanstalkInterface::DEFAULT_DELAY;
		if($ttr===null)
			$ttr= PheanstalkInterface::DEFAULT_TTR;

		$retryProperty=self::RETRY_PROPERTY;
		if(property_exists($data,$retryProperty)===false)	//track how many times we've retried to allow for limited retries
			$data->$retryProperty=0;

		$encoded=json_encode($data);
		if($encoded===false)
			self::$log->error($pool.': json encode failed.');
		if(strlen(trim($encoded))===0)
			self::$log->error($pool.': Tried to add empty data.');
		else
		{
			$queue = Pheanstalk::create('127.0.0.1');
			$queue->useTube($pool);
			$job=$queue->put($encoded,$priority,$delay,$ttr);
			self::$log->info("Add ".$pool.'.'.$job->getId().": ".$encoded);

		}
	}
}
