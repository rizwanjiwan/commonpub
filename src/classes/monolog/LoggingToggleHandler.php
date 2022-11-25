<?php
/**
 * Allows the Log Manager to control if logging should be on or off without forcing everyone to get new loggers
 */

namespace rizwanjiwan\common\classes\monolog;

use rizwanjiwan\common\classes\LogManager;
use Monolog\Handler\AbstractHandler;

class LoggingToggleHandler extends AbstractHandler
{

	/**
	 * Handles a record.
	 *
	 * All records may be passed to this method, and the handler should discard
	 * those that it does not want to handle.
	 *
	 * The return value of this function controls the bubbling process of the handler stack.
	 * Unless the bubbling is interrupted (by returning true), the Logger class will keep on
	 * calling further handlers in the stack with a given log record.
	 *
	 * @param  array $record The record to handle
	 * @return Boolean true means that this handler handled the record, and that bubbling is not permitted.
	 *                        false means the record was either not processed or that this handler allows bubbling.
	 */
	public function handle(\Monolog\LogRecord $logRecord): bool
	{
        $record=$logRecord->toArray();
        return LogManager::isLoggingOn()===false;
	}
}