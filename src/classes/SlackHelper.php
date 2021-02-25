<?php


namespace rizwanjiwan\common\classes;


use Slack;
use SlackMessage;

/**
 * Send messages via slack webhook
 * Class SlackHelper
 * @package rizwanjiwan\common\classes
 */
class SlackHelper
{

    public static function sendMessage(string $message)
    {
        if(Config::get(('SLACK_WEBHOOK_URL'))!==null)
        {
            self::sendMessageReal($message,Config::get('SLACK_WEBHOOK_URL'));
        }

    }

    protected static function sendMessageReal(string $message, string $webhookUrl)
    {
        $slackMessage=new SlackMessage(new Slack($webhookUrl));
        $slackMessage->setText($message);
        $slackMessage->send();

    }
}