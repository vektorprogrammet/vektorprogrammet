<?php

namespace AppBundle\Sms;

class SmsSender implements SmsSenderInterface
{
    private $smsSender;

    public function __construct(string $env, GatewayAPI $gatewayAPI, SlackSms $slackSms)
    {
        if ($env === 'prod') {
            $this->smsSender = $gatewayAPI;
        } else {
            $this->smsSender = $slackSms;
        }
    }

    public function send(Sms $sms)
    {
        $this->smsSender->send($sms);
    }

    public function validatePhoneNumber(string $number): bool
    {
        return $this->smsSender->validatePhoneNumber($number);
    }
}
