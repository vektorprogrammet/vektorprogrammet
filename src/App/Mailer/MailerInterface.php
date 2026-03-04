<?php

namespace App\Mailer;

use Symfony\Component\Mime\Email;

interface MailerInterface
{
    public function send(Email $message, bool $disableLogging = false);
}
