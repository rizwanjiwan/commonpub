<?php


namespace rizwanjiwan\common\classes\monolog;


use Monolog\Handler\AbstractProcessingHandler;

class StdOutHandler extends AbstractProcessingHandler
{


	/**
	 * Writes the record down to the log of the implementing handler
	 *
	 * @param  $record[]
	 * @return void
	 */
	protected function write(\Monolog\LogRecord $record): void
	{
        $recordArray=$record->toArray();
		echo "[".$recordArray['datetime']->format('Y-m-d H:i:s')."][".$recordArray['level']."][".$recordArray['channel']."] ".$recordArray['message']."\n";
	}
}
