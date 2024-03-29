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
	protected function write(array $record): void
	{
        echo "[".$record['datetime']->format('Y-m-d H:i:s')."][".$record['level']."][".$record['channel']."] ".$record['message']."\n";
	}
}
