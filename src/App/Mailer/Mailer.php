<?php

namespace App\Mailer;

use App\Google\Gmail;
use App\Service\SlackMailer;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailer implements MailerInterface
{
    private $gmail;
    private $slackMailer;
    private $symfonyMailer;
    private $env;

    public function __construct(string $env, Gmail $gmail, SymfonyMailerInterface $symfonyMailer, SlackMailer $slackMailer)
    {
        $this->env = $env;
        $this->gmail = $gmail;
        $this->symfonyMailer = $symfonyMailer;
        $this->slackMailer = $slackMailer;
    }

    public function send(Email $message, bool $disableLogging = false)
    {
        // Gmail::send() always sets a from address in prod.
        // For other environments, add a default from if none is set.
        if (empty($message->getFrom())) {
            $message->from(new Address('noreply@vektorprogrammet.no', 'Vektorprogrammet'));
        }

        if ($this->env === 'prod') {
            $this->gmail->send($message, $disableLogging);
        } elseif ($this->env === 'staging') {
            $this->slackMailer->send($message);
        } else {
            $this->symfonyMailer->send($message);
        }
    }
}
