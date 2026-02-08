<?php

namespace App\Service;

use App\Mailer\MailerInterface;
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
        $from = $message->getFrom();
        $fromString = !is_array($from) ? $from : current($from) . ' - ' . key($from);

        $this->messenger->sendPayload([
            'text' => 'Email sent',
            'attachments' => [[
                'color' => '#023874',
                'author_name' => 'To: ' . implode(', ', array_keys($message->getTo())),
                'text' => '*' . $message->getSubject() . "*\n```\n" . $message->getBody() . "\n```",
                'footer' => 'From: ' . $fromString,
            ]],
        ]);
    }
}
