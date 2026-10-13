<?php
/**
 * Encapsulates the configuration file to provide runtime behaviour information.
 */

namespace rizwanjiwan\common\classes;
//todo: deal with multibyte strings at some point: https://phptherightway.com/#php_and_utf8


use rizwanjiwan\common\classes\exceptions\FileNotExistException;

class Config
{
	/**
	 * Holds the key value paris
	 * @var ?array
	 */
	private static ?array $envVars=null;

    private static ?string $path=null;  //path to the config file

    /**
     * Initialize this static
     * @param string $path path relative to this file to open the config
     * @throws FileNotExistException
     */
	public static function init(string $path='/../../../../../config')
	{
		if(self::$envVars!==null)
			return;
        if(file_exists(realpath(dirname(__FILE__)).$path)===false)
        {
            if(getenv('CONFIG_ENV')!=='true'){
                throw new FileNotExistException('Config file not found: '.$path.'. Set CONFIG_ENV=true to use environment variables.');
            }
            self::$envVars=array();
            //grab env vars:
            foreach(getenv() as $key=>$val) {
                Config::$envVars[$key]=$val;
            }
        }
        else{   //parse file
            self::$envVars=array();
            $fh=fopen(realpath(dirname(__FILE__)).$path,'r');
            self::$path=$path;
            while (($line = fgets($fh)) !== false)
            {
                $line=trim($line);
                if((strlen($line)===0)||(strpos($line,'#')!==0))	//skip comment lines and blanks
                {
                    $lineSplit=explode('=',$line,2);
                    $key=trim($lineSplit[0]);
                    if(strcmp($key,'')!==0)
                        Config::$envVars[trim($key)]=trim($lineSplit[1]);
                }
            }
        }


	}

	/**
	 * Get the full path to the tmp directory (starts and ends with a '/')
	 * @return string
	 */
	public static function getTmpDir():string
	{
		$tmpDir=self::get('TMP_DIR');
		if($tmpDir===false)
			return '/tmp';
		return realpath(dirname(__FILE__)).$tmpDir;
	}


	/**
	 * Get the value for a given key or false if the key doesn't exist in the config
	 * @param $key string
	 * @return string|boolean false if key doesn't exist
	 */
	public static function get(string $key):string|bool
	{
		self::init();
		if(array_key_exists($key,self::$envVars))
			return self::$envVars[$key];
		return false;
	}
	/**
	 * Get the value for a given key or false if the key doesn't exist in the config. This will comma split the values into an array
	 * @param $key string
	 * @return string[]|boolean false if key doesn't exist
	 */
	public static function getArray(string $key): array|bool
	{
		$val=self::get($key);
		if($val===false)
			return false;
		$vals=explode(',',$val);
		$trimmedVals=array();
		foreach($vals as $val)
			array_push($trimmedVals,trim($val));
		return $trimmedVals;
	}

	/**
	 * Get the value for a given key as a boolean
	 * @param $key string key from the config
	 * @return bool true if value is 'true'
	 */
	public static function getBool(string $key):bool
	{
		$val=self::get($key);
        if($val===false){
            return false;
        }
		return strcasecmp($val,'true')===0;
	}

    /**
     * Set a value
     * @param $key string
     * @param $value string
     * @param bool $persist true to persist to config file
     */
	public static function set(string $key,string $value,bool $persist=false):void
	{
		self::init();
		self::$envVars[$key]=$value;
        if($persist){
            if(self::$path===null){
                putenv("$key=$value");
            }
            else{  //save to file
                //read the file in one line at a time and overwrite as needed
                $path=realpath(dirname(__FILE__)).self::$path;
                $oldFileContents=file($path,FILE_IGNORE_NEW_LINES);
                $newFileContents='';
                foreach($oldFileContents as $line)
                {
                    $line=trim($line);
                    if((strlen($line)===0)||(strpos($line,'#')!==0))	//process normal line
                    {
                        $lineSplit=explode('=',$line,2);
                        $key=trim($lineSplit[0]);
                        if(strcmp($key,'')!==0 && (array_key_exists($key,self::$envVars))){
                            $newFileContents.=$key.'='.self::$envVars[$key].PHP_EOL;
                        }
                        else{   //pass through
                            $newFileContents.=$line.PHP_EOL;
                        }
                    }
                    else{   //just pass through
                        $newFileContents.=$line.PHP_EOL;
                    }
                }
                file_put_contents($path,$newFileContents);  //done
            }
        }
	}
}