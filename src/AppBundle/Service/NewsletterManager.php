<?php

namespace AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Subscriber;
use AppBundle\Mailer\MailerInterface;
use Doctrine\ORM\EntityManager;

class NewsletterManager
{
    private $mailer;
    private $twig;
    private $em;

    /**
     * LetterManager constructor.
     *
     * @param MailerInterface   $mailer
     * @param \Twig_Environment $twig
     * @param EntityManager     $em
     */
    public function __construct(MailerInterface $mailer, \Twig_Environment $twig, EntityManager $em)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->em = $em;
    }

    public function subscribeToCheckedNewsletter($department, $name, $email)
    {
        $newsletter = $this->em->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);
        $this->subscribe($newsletter, $name, $email);
    }

    public function subscribe($newsletter, $name, $email)
    {
        if ($newsletter === null) {
            return;
        }
        $subscriber = new Subscriber();
        $subscriber->setName($name);
        $subscriber->setEmail($email);

        $alreadySubscribed = count($this->em->getRepository('AppBundle:Subscriber')->
            findByEmailAndNewsletter($subscriber->getEmail(), $newsletter)) > 0;

        // Check if the user is already subscribed
        if (!$alreadySubscribed) {
            $subscriber->setNewsletter($newsletter);
            $this->em->persist($subscriber);
            $this->em->flush();
        }
    }

    public function send(Letter $letter)
    {
        $newsletter = $letter->getNewsletter();
        $applicantMailAddresses = array();

        $department = $newsletter->getDepartment();
        $activeSemester = $this->em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

        if($activeSemester and $letter->getExcludeApplicants()){
            $applications = $this->em->getRepository('AppBundle:Application')->findNewApplicants($department, $activeSemester);
            $applicantMailAddresses = array_map(function(Application $application){
                return $application->getUser()->getEmail();
            }, $applications);
        }

        foreach ($newsletter->getSubscribers() as $subscriber) {
            $subscriberMail = $subscriber->getEmail();

            if(in_array($subscriberMail, $applicantMailAddresses)){
                continue;
            }

            $message = \Swift_Message::newInstance()
                ->setSubject($letter->getTitle())
                ->setFrom(array(
                    $newsletter->getDepartment()->getEmail() => 'Vektorprogrammet',
                ))
                ->setTo($subscriberMail)
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
