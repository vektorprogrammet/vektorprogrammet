<?php


namespace AppBundle\Service;

use AppBundle\Entity\AdmissionNotification;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class AdmissionNotifier
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var EmailSender
     */
    private $emailSender;
    private $logger;

    public function __construct(EntityManager $em, EmailSender $emailSender, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->emailSender = $emailSender;
        $this->logger = $logger;
    }

    public function sendAdmissionNotifications()
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findActive();
        try {
            foreach ($departments as $department) {
                $semester = $department->getCurrentSemester();
                if (!$semester || !$semester->hasActiveAdmission()) {
                    continue;
                }
                $subscribers = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByDepartment($department);
                foreach ($subscribers as $subscriber) {
                    $alreadyNotified = $this->em->getRepository('AppBundle:AdmissionNotification')->findBySubscriberAndSemester($subscriber, $semester);
                    if (!$alreadyNotified) {
                        $this->emailSender->sendAdmissionStartedNotification($subscriber);
                        $notification = new AdmissionNotification();
                        $notification->setSemester($semester);
                        $notification->setSubscriber($subscriber);
                        $this->em->persist($notification);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Failed to send admission notifiction:\n".$e->getMessage());
        } finally {
            $this->em->flush();
        }
    }
}
