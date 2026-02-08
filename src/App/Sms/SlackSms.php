<?php

namespace App\Sms;

use App\Service\SlackMessenger;

class SlackSms implements SmsSenderInterface
{
    private $slackMessenger;

    public function __construct(SlackMessenger $slackMessenger)
    {
        $this->slackMessenger = $slackMessenger;
    }

    public function send(Sms $sms)
    {
        $this->slackMessenger->sendPayload([
            'text' => 'Sms sent',
            'attachments' => [[
                'color' => '#28a745',
                'author_name' => 'To: ' . $sms->getRecipientsString(),
                'text' => "```\n" . $sms->getMessage() . "\n```",
            ]],
        ]);
    }

    public function validatePhoneNumber(string $number): bool
    {
        return true;
    }
}
