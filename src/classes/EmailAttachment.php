<?php
/**
 * Encapsulates information about an email attachment
 */

namespace rizwanjiwan\common\classes;



use rizwanjiwan\common\classes\exceptions\FileNotExistException;

class EmailAttachment
{

	private $path;
	private $fileNameOverride;

	/**
	 * EmailAttachment constructor.
	 * @param $path string the path to the file to attach
	 * @param $fileNameOverride string|null a file name to use for the file when attached
	 * @throws FileNotExistException if file doesn't exist
	 */
	public function __construct($path, $fileNameOverride=null)
	{
		if(file_exists($path)===false)
			throw new FileNotExistException('File must exist to be an attachment: '.$path);
		$this->path=$path;
		$this->fileNameOverride=$fileNameOverride;
	}

	/**
	 * Nice method that makes chaining easier
	 * @param $path string the path to the file to attach
	 * @param $fileNameOverride string|null a file name to use for the file when attached
	 * @return EmailAttachment
	 * @throws FileNotExistException if file doesn't exist
	 */
	public static function create($path, $fileNameOverride=null)
	{
			return new self($path,$fileNameOverride);
	}

	public function path()
	{
		return $this->path;
	}
	public function fileName()
	{
		if($this->fileNameOverride!==null)
			return $this->fileNameOverride;
		return basename($this->path);
	}
}