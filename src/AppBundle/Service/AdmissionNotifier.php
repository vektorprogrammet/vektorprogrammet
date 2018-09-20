<?php


namespace AppBundle\Service;

use AppBundle\Entity\AdmissionNotification;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdmissionNotifier
{
    private $em;
    private $emailSender;
    private $logger;
    private $validator;
    private $sendLimit;

    public function __construct(EntityManager $em, EmailSender $emailSender, LoggerInterface $logger, ValidatorInterface $validator, int $sendLimit)
    {
        $this->em = $em;
        $this->emailSender = $emailSender;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->sendLimit = $sendLimit;
    }

    /**
     * @param Department $department
     * @param string $email
     * @param bool $infoMeeting
     * @param bool $fromApplication
     *
     * @throws \InvalidArgumentException
     */
    public function createSubscription(Department $department, string $email, bool $infoMeeting = false, bool $fromApplication = false)
    {
        $alreadySubscribed = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByEmailAndDepartment($email, $department);
        if ($alreadySubscribed) {
            return;
        }

        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);
        $subscriber->setEmail($email);
        $subscriber->setInfoMeeting($infoMeeting);
        $subscriber->setFromApplication($fromApplication);

        $errors = $this->validator->validate($subscriber);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $this->em->persist($subscriber);
        $this->em->flush();
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

                $applicationEmails = $this->em->getRepository('AppBundle:Application')->findEmailsBySemester($semester);
                $subscribers = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByDepartment($department);
                $notificationEmails = $this->em->getRepository('AppBundle:AdmissionNotification')->findEmailsBySemester($semester);

                $notificationsSent = 0;
                foreach ($subscribers as $subscriber) {
                    if ($notificationsSent > $this->sendLimit) {
                        break;
                    }
                    $hasApplied = array_search($subscriber->getEmail(), $applicationEmails) !== false;
                    $alreadyNotified = array_search($subscriber->getEmail(), $notificationEmails) !== false;
                    $subscribedMoreThanOneYearAgo = $subscriber->getTimestamp()->diff(new \DateTime())->y >= 1;
                    if ($hasApplied || $alreadyNotified || $subscribedMoreThanOneYearAgo) {
                        continue;
                    }

                    $this->emailSender->sendAdmissionStartedNotification($subscriber);
                    $notification = new AdmissionNotification();
                    $notification->setSemester($semester);
                    $notification->setSubscriber($subscriber);
                    $this->em->persist($notification);
                    $notificationsSent++;

                    usleep(50 * 1000); // 50ms
                }
                if ($notificationsSent > 0) {
                    $this->logger->info("*$notificationsSent* admission notification emails sent to subscribers in *" . $department->getCity() . "*");
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Failed to send admission notifiction:\n".$e->getMessage());
        } finally {
            $this->em->flush();
        }
    }

    public function sendInfoMeetingNotifications()
    {
        $departments = $this->em->getRepository('AppBundle:Department')->findActive();
        try {
            foreach ($departments as $department) {
                $semester = $department->getCurrentSemester();
                if (!isset($semester) || is_null($semester->getInfoMeeting())){
                    continue;
                }

                $infoMeetingLessThanTwoDays = $semester->getInfoMeeting()->getDate()->diff(new \DateTime())->d <= 2;
                if (!$infoMeetingLessThanTwoDays) {
                    continue;
                }

                $applicationEmails = $this->em->getRepository('AppBundle:Application')->findEmailsBySemester($semester);
                $subscribers = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByDepartment($department);
                $notificationEmails = $this->em->getRepository('AppBundle:AdmissionNotification')->findEmailsBySemester($semester);

                $notificationsSent = 0;
                foreach ($subscribers as $subscriber) {
                    if ($notificationsSent > $this->sendLimit) {
                        break;
                    }
                    $hasApplied = array_search($subscriber->getEmail(), $applicationEmails) !== false;
                    $alreadyNotified = array_search($subscriber->getEmail(), $notificationEmails) !== false;
                    $subscribedMoreThanOneYearAgo = $subscriber->getTimestamp()->diff(new \DateTime())->y >= 1;
                    if ($hasApplied || $alreadyNotified || $subscribedMoreThanOneYearAgo || !$subscriber->getInfoMeeting()) {
                        continue;
                    }
                    $this->emailSender->sendInfoMeetingNotification($subscriber);
                    $notification = new AdmissionNotification();
                    $notification->setSemester($semester);
                    $notification->setSubscriber($subscriber);
                    $this->em->persist($notification);
                    $notificationsSent++;

                    usleep(50 * 1000); // 50ms
                }
                if ($notificationsSent > 0) {
                    $this->logger->info("*$notificationsSent* info meeting notification emails sent to subscribers in *" . $department->getCity() . "*");
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Failed to send info meeting notifiction:\n".$e->getMessage());
        } finally {
            $this->em->flush();
        }
    }
}
