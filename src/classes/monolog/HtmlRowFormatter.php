<?php
/**
 * Will format each message as a table row with the layout of:
 * [log level][channel][time][message]
 */

namespace rizwanjiwan\common\classes\monolog;


use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;

class HtmlRowFormatter extends NormalizerFormatter
{
	/**
	 * Translates Monolog log levels to html color priorities.
	 */
	protected static $logLevels = [
		Logger::DEBUG     => '#cccccc',
		Logger::INFO      => '#468847',
		Logger::NOTICE    => '#3a87ad',
		Logger::WARNING   => '#c09853',
		Logger::ERROR     => '#f0ad4e',
		Logger::CRITICAL  => '#FF7708',
		Logger::ALERT     => '#C12A19',
		Logger::EMERGENCY => '#000000',
	];

	public function __construct()
	{
		parent::__construct(null);
	}

	/**
	 * Formats a log record.
	 *
	 * @param  array $record A record to format
	 * @return string The formatted record
	 */
	public function format(\Monolog\LogRecord $logRecord)
	{
        $record=$logRecord->toArray();
        $output = '<tr style="border:1px;">';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: '.self::$logLevels[$record['level']].'">'.htmlentities($record['level_name']).'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($record['channel']).'</td>';
$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($record['extra']['class'].'::'.$record['extra']['function']."(".$record['extra']['line'].")").'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($record['datetime']->format('H:i:s')).'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee"><pre>'.htmlentities($record['message']).'</pre></td>';
		$output.='</tr>';
		return $output;
	}
	/**
	 * Formats a set of log records.
	 *
	 * @param  array $records A set of records to format
	 * @return mixed The formatted set of records
	 */
	public function formatBatch(array $records): string
	{
		$message = '';
		foreach ($records as $record) {
			$message .= $this->format($record);
		}
		return $message;
	}
}