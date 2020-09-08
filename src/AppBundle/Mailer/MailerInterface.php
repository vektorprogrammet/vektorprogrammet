<?php

namespace AppBundle\Mailer;

use Swift_Message;

interface MailerInterface
{
    public function send(Swift_Message $message, bool $disableLogging = false);
}
