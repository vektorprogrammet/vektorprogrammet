<?php

namespace App\Support\Infrastructure\Sms;

use App\Support\Infrastructure\Slack\SlackMessenger;

class SlackSms implements SmsSenderInterface
{
    public function __construct(private readonly SlackMessenger $slackMessenger)
    {
    }

    public function send(Sms $sms)
    {
        $this->slackMessenger->sendPayload([
            'text' => 'Sms sent',
            'attachments' => [[
                'color' => '#28a745',
                'author_name' => 'To: '.$sms->getRecipientsString(),
                'text' => "```\n".$sms->getMessage()."\n```",
            ]],
        ]);
    }

    public function validatePhoneNumber(string $number): bool
    {
        return true;
    }
}
