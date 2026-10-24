<?php

namespace AppBundle\Mailer;

final class SwiftMailerFactory
{
    // Production mail uses the Gmail adapter. Local mail must never leave the machine.
    public static function create(\AppBundle\DataCollector\MailCollector $collector): \Swift_Mailer
    {
        $mailer = new \Swift_Mailer(new \Swift_NullTransport());
        $mailer->registerPlugin($collector);
        return $mailer;
    }
}
