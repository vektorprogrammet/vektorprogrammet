<?php

namespace App\Mailer;

use App\Google\Gmail;
use App\Service\SlackMailer;
use Symfony\Component\Mailer\MailerInterface as SymfonyMailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailer implements MailerInterface
{
    public function __construct(private readonly string $env, private readonly Gmail $gmail, private readonly SymfonyMailerInterface $symfonyMailer, private readonly SlackMailer $slackMailer)
    {
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
