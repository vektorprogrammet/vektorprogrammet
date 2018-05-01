<?php

namespace AppBundle\Mailer;

use AppBundle\Google\Gmail;

class Mailer implements MailerInterface
{
    private $mailer;

    public function __construct(string $env, Gmail $gmail, \Swift_Mailer $swiftMailer)
    {
        if ($env === 'prod') {
            $this->mailer = $gmail;
        } else {
            $this->mailer = $swiftMailer;
        }
    }

    public function send(\Swift_Message $message, bool $disableLogging = false)
    {
        if ($this->mailer instanceof Gmail) {
            $this->mailer->send($message, $disableLogging);
        } else {
            $this->mailer->send($message);
        }
    }
}
