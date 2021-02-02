<?php

namespace AppBundle\Mailer;

use AppBundle\Google\Gmail;
use AppBundle\Service\SlackMailer;
use Swift_Mailer;
use Swift_Message;

class Mailer implements MailerInterface
{
    private $mailer;

    public function __construct(string $env, Gmail $gmail, Swift_Mailer $swiftMailer, SlackMailer $slackMailer)
    {
        if ($env === 'prod') {
            $this->mailer = $gmail;
        } elseif ($env === 'staging') {
            $this->mailer = $slackMailer;
        } else {
            $this->mailer = $swiftMailer;
        }
    }

    public function send(Swift_Message $message, bool $disableLogging = false)
    {
        if ($this->mailer instanceof Gmail) {
            $this->mailer->send($message, $disableLogging);
        } else {
            $this->mailer->send($message);
        }
    }
}
