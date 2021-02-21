<?php


namespace rizwanjiwan\common\classes\jobs;


use rizwanjiwan\common\classes\EmailHelper;
use rizwanjiwan\common\classes\exceptions\MailException;
use stdClass;

class SendEmailJobProcessor implements JobProcessorInterface
{

    /**
     * Process a job
     * @param $data stdClass of the data to use in processing for this job
     * @throws MailException
     */
    public function process($data)
    {
        EmailHelper::doSendMailJob($data);
    }
}