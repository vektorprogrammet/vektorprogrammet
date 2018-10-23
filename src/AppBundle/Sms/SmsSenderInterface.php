<?php

namespace AppBundle\Sms;

interface SmsSenderInterface
{
    public function send(Sms $sms);
    public function validatePhoneNumber(string $number): bool;
}
