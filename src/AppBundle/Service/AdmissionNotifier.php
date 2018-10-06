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
     * @param bool $fromApplication
     *
     * @throws \InvalidArgumentException
     */
    public function createSubscription(Department $department, string $email, bool $fromApplication = false)
    {
        $alreadySubscribed = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByEmailAndDepartment($email, $department);
        if ($alreadySubscribed) {
            return;
        }

        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);
        $subscriber->setEmail($email);
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
        $semester = $this->em->getRepository('AppBundle:Semester')->findCurrentSemester();
        try {
            foreach ($departments as $department) {
                $admissionPeriod = $this->em->getRepository('AppBundle:AdmissionPeriod')->findOneByDepartmentAndSemester($department, $semester);
                if ($admissionPeriod === null || !$admissionPeriod->hasActiveAdmission()) {
                    continue;
                }
                $applicationEmails = $this->em->getRepository('AppBundle:Application')->findEmailsByAdmissionPeriod($admissionPeriod);
                $subscribers = $this->em->getRepository('AppBundle:AdmissionSubscriber')->findByDepartment($department);
                $notificationEmails = $this->em->getRepository('AppBundle:AdmissionNotification')->findEmailsBySemesterAndDepartment($semester, $department);

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
}
