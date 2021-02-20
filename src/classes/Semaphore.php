<?php


namespace rizwanjiwan\common\classes;
/**
 * Encapsulates semaphore like behaviour to provide mutex
 * Class Semaphore
 * @package rizwanjiwan\common\classes
 */
class Semaphore
{

    /**
     * @var string the path to the lock file
     */
    private string $lockFile;

    /**
     * @var resource lock on file
     */
    private $resource=null;

    /**
     * Semaphore constructor.
     * @param $uniqueSemaphoreId int the semaphore ID to mutext on
     */
    public function __construct($uniqueSemaphoreId)
    {
        $tmpDir=Config::get('TMP_DIR');
        $this->lockFile=$tmpDir.'semaphore_'.$uniqueSemaphoreId.'.lock';
    }

    /**
     * Start mutex
     */
    public function mutex_start()
    {
        if($this->resource!==null)
            return;//already locked
        if(file_exists($this->lockFile)===false)
            touch($this->lockFile);
        $this->resource=fopen($this->lockFile,'rw');
        flock($this->resource,LOCK_EX);
    }
    /**
     * End mutex
     */
    public function mutex_end()
    {
        if($this->resource!==null)
            fclose($this->resource);
        $this->resource=null;
    }
}