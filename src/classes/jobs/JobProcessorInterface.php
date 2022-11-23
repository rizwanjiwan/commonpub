<?php
/**
 * Defines an interface for anything that will process a job
 */

namespace rizwanjiwan\common\classes\jobs;


use stdClass;

interface JobProcessorInterface
{
	/**
	 * Process a job
	 * @param $data stdClass of the data to use in processing for this job
	 */
	public function process(stdClass $data);

}