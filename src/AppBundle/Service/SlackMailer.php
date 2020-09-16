<?php

namespace AppBundle\Service;

use AppBundle\Mailer\MailerInterface;
use Nexy\Slack\Attachment;
use Swift_Message;

class SlackMailer implements MailerInterface
{
    private $messenger;

    public function __construct(SlackMessenger $messenger)
    {
        $this->messenger = $messenger;
    }

    public function send(Swift_Message $message, bool $disableLogging = false)
    {
        $slackMessage = $this->messenger->createMessage();
        $attachment = new Attachment();
        $attachment->setColor("#023874");
        $attachment->setAuthorName("To: " . implode(", ", array_keys($message->getTo())));
        $attachment->setText("*".$message->getSubject() . "*\n```\n".$message->getBody()."\n```");

        $from = $message->getFrom();
        $attachment->setFooter("From: " . (!is_array($from) ? $from : current($from) . " - " . key($from)));
        
        $slackMessage->setText("Email sent");
        $slackMessage->setAttachments([$attachment]);

        $this->messenger->send($slackMessage);
    }
}
