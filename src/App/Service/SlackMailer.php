<?php

namespace App\Service;

use App\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SlackMailer implements MailerInterface
{
    public function __construct(
        private readonly SlackMessenger $messenger,
    ) {
    }

    public function send(Email $message, bool $disableLogging = false)
    {
        $toAddresses = $message->getTo();
        $toStr = implode(', ', array_map(fn (Address $a) => $a->getAddress(), $toAddresses));

        $fromAddresses = $message->getFrom();
        $fromStr = '';
        if (!empty($fromAddresses)) {
            $first = $fromAddresses[0];
            $fromStr = $first->getName() ? $first->getName().' - '.$first->getAddress() : $first->getAddress();
        }

        $body = $message->getHtmlBody() ?: $message->getTextBody();

        $this->messenger->sendPayload([
            'text' => 'Email sent',
            'attachments' => [[
                'color' => '#023874',
                'author_name' => 'To: '.$toStr,
                'text' => '*'.$message->getSubject()."*\n```\n".$body."\n```",
                'footer' => 'From: '.$fromStr,
            ]],
        ]);
    }
}
