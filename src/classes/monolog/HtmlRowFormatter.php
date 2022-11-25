<?php
/**
 * Will format each message as a table row with the layout of:
 * [log level][channel][time][message]
 */

namespace rizwanjiwan\common\classes\monolog;


use Monolog\Formatter\NormalizerFormatter;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;

class HtmlRowFormatter extends NormalizerFormatter
{

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
     * Translates Monolog log levels to html color priorities.
     */
    private static function levelToColours(Level $level):string
    {
        if($level===Level::Debug){
            return "#cccccc";
        }elseif($level===Level::Info){
            return "#468847";
        }elseif($level===Level::Notice){
            return "#3a87ad";
        }elseif($level===Level::Warning){
            return "#c09853";
        }elseif ($level===Level::Error){
            return "#f0ad4e";
        }elseif($level===Level::Critical){
            return "#FF7708";
        }elseif ($level===Level::Alert){
            return "#C12A19";
        }
        return "#000000";
    }
	/**
	 * Formats a log record.
	 *
	 * @param  LogRecord $record A record to format
	 * @return string The formatted record
	 */
	public function format(\Monolog\LogRecord $record): string
    {
        $recordArray=$record->toArray();
		$output = '<tr style="border:1px;">';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: '.self::levelToColours(Level::from($recordArray['level'])).'">'.htmlentities($record['level_name']).'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($recordArray['channel']).'</td>';
$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($recordArray['extra']['class'].'::'.$record['extra']['function']."(".$record['extra']['line'].")").'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee">'.htmlentities($recordArray['datetime']->format('H:i:s')).'</td>';
		$output.='<td style="border:1px;padding: 4px;text-align: left;background: #eeeeee"><pre>'.htmlentities($recordArray['message']).'</pre></td>';
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