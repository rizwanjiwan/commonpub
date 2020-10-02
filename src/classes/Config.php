<?php
/**
 * Encapsulates the configuration file to provide runtime behaviour information.
 */

namespace rizwanjiwan\common\classes;
//todo: deal with multibyte strings at some point: https://phptherightway.com/#php_and_utf8


class Config
{
	/**
	 * Holds the key value paris
	 * @var array
	 */
	private static $envVars=null;

    /**
     * Initialize this static
     * @param string $path path relative to this file to open the config
     */
	public static function init($path='/../../../../../config')
	{
		if(self::$envVars!==null)
			return;
		self::$envVars=array();
		$fh=fopen(realpath(dirname(__FILE__)).$path,'r');
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

	/**
	 * Get the full path to the tmp directory (starts and ends with a '/')
	 * @return string
	 */
	public static function getTmpDir()
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
	public static function get($key)
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
	public static function getArray($key)
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
	public static function getBool($key)
	{
		$val=self::get($key);
		return strcasecmp($val,'true')===0;
	}

	/**
	 * Set a value
	 * @param $key string
	 * @param $value string
	 */
	public static function set($key,$value)
	{
		self::init();
		self::$envVars[$key]=$value;
	}
}