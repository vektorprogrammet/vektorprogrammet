<?php

namespace AppBundle\Mailer;

interface MailerInterface
{
    public function send(\Swift_Message $message);
}
