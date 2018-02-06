<?php

namespace AppBundle\Service;

interface MailerInterface
{
    public function send(\Swift_Message $message);
}
