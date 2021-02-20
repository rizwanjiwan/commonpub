<?php
/**
 * Encapsulates information about an email attachment
 */

namespace rizwanjiwan\common\classes;



use rizwanjiwan\common\classes\exceptions\FileNotExistException;

class EmailAttachment
{

	private string $path;
	private ?string $fileNameOverride;

	/**
	 * EmailAttachment constructor.
	 * @param $path string the path to the file to attach
	 * @param $fileNameOverride string|null a file name to use for the file when attached
	 * @throws FileNotExistException if file doesn't exist
	 */
	public function __construct(string $path, ?string $fileNameOverride=null)
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
	public static function create(string $path, ?string $fileNameOverride=null):EmailAttachment
	{
			return new self($path,$fileNameOverride);
	}

	public function path():string
	{
		return $this->path;
	}
	public function fileName():string
	{
		if($this->fileNameOverride!==null)
			return $this->fileNameOverride;
		return basename($this->path);
	}
}