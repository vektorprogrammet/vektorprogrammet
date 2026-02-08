<?php

namespace App\Mailer;

use Swift_Message;

interface MailerInterface
{
    public function send(Swift_Message $message, bool $disableLogging = false);
}
