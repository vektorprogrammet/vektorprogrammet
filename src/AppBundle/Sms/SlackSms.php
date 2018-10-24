<?php

namespace AppBundle\Sms;

use AppBundle\Service\SlackMessenger;
use Nexy\Slack\Attachment;

class SlackSms implements SmsSenderInterface
{
    private $slackMessenger;

    public function __construct(SlackMessenger $slackMessenger)
    {
        $this->slackMessenger = $slackMessenger;
    }

    public function send(Sms $sms)
    {
        $message = $this->slackMessenger->createMessage();

        $attachment = new Attachment();
        $attachment->setColor("#28a745");
        $attachment->setAuthorName("To: " . $sms->getRecipientsString());
        $attachment->setText("```\n" . $sms->getMessage() . "\n```");

        $message->setText("Sms sent");
        $message->setAttachments([$attachment]);

        $this->slackMessenger->send($message);
    }

    public function validatePhoneNumber(string $number): bool
    {
        return true;
    }
}
