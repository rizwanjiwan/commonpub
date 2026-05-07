<?php


namespace rizwanjiwan\common\classes\monolog;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class StdOutHandler extends AbstractProcessingHandler
{


	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  LogRecord $record
	 * @return void
	 */
	protected function write(LogRecord $record): void
	{
        $record=$record->toArray();
        echo "[".$record['datetime']->format('Y-m-d H:i:s')."][".$record['level']."][".$record['channel']."] ".$record['message']."\n";
	}
}
