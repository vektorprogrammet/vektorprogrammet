<?php

namespace AppBundle\Sms;

interface SmsSender
{
    public function send(Sms $sms);
    public function validatePhoneNumber(string $number, string $countryCode): bool;
}
