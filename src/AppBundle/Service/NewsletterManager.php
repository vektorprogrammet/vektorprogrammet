<?php

namespace AppBundle\Service;

use AppBundle\Entity\Letter;

class NewsletterManager
{
    private $mailer;
    private $twig;

    /**
     * LetterManager constructor.
     *
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send(Letter $letter)
    {
        $newsletter = $letter->getNewsletter();

        foreach ($newsletter->getSubscribers() as $subscriber) {
            $message = \Swift_Message::newInstance()
                ->setSubject($letter->getTitle())
                ->setFrom(array(
                    $newsletter->getDepartment()->getEmail() => 'Vektorprogrammet',
                ))
                ->setTo($subscriber->getEmail())
                ->setBody(
                    $this->twig->render(
                        'newsletter/mail_template.html.twig',
                        array(
                            'name' => $subscriber->getName(),
                            'department' => $newsletter->getDepartment()->getShortName(),
                            'content' => $letter->getContent(),
                            'unsubscribeCode' => $subscriber->getUnsubscribeCode(),
                        )
                    ),
                    'text/html'
                )
                ->setContentType('text/html');

            $this->mailer->send($message);
        }
    }
}
