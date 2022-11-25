<?php
/**
 * Buffers the output so you can grab it later
 */

namespace rizwanjiwan\common\classes\monolog;


use Monolog\Handler\AbstractProcessingHandler;

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
	 * @param  array $record
	 * @return void
	 */
	protected function write(\Monolog\LogRecord $record): void
	{
		self::$buffer.=(string)$record->toArray()['formatted'];
	}
}