<?php
/**
 * Buffers the output so you can grab it later
 */

namespace rizwanjiwan\common\classes\monolog;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class LoggingBufferHandler extends AbstractProcessingHandler
{
	private static $buffer="";


	/**
	 * @param $clear boolean true to clear the buffer at the same time
	 * @return string the buffered content
	 */
	public static function getBuffer($clear=true)
	{
		$buff=self::$buffer;
		if($clear)
			self::$buffer="";
		return $buff;
	}

	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  LogRecord $record
	 * @return void
	 */
	protected function write(LogRecord $record): void
	{
        $record=$record->toArray();
		self::$buffer.=(string)$record['formatted'];
	}
}
