<?php

namespace App\Admission\Infrastructure;

use App\Admission\Infrastructure\Entity\AdmissionNotification;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\AdmissionSubscriber;
use App\Admission\Infrastructure\Entity\Application;
use App\Entity\Department;
use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdmissionNotifier
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EmailSender $emailSender,
        private readonly LoggerInterface $logger,
        private readonly ValidatorInterface $validator,
        private readonly int $sendLimit,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createSubscription(Department $department, string $email, bool $infoMeeting = false, bool $fromApplication = false)
    {
        $alreadySubscribed = $this->em->getRepository(AdmissionSubscriber::class)->findByEmailAndDepartment($email, $department);
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
        $departments = $this->em->getRepository(Department::class)->findActive();
        $semester = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester();
        try {
            foreach ($departments as $department) {
                $admissionPeriod = $this->em->getRepository(AdmissionPeriod::class)->findOneByDepartmentAndSemester($department, $semester);
                if ($admissionPeriod === null || !$admissionPeriod->hasActiveAdmission()) {
                    continue;
                }
                $applicationEmails = $this->em->getRepository(Application::class)->findEmailsByAdmissionPeriod($admissionPeriod);
                $subscribers = $this->em->getRepository(AdmissionSubscriber::class)->findByDepartment($department);
                $notificationEmails = $this->em->getRepository(AdmissionNotification::class)->findEmailsBySemesterAndDepartment($semester, $department);

                $notificationsSent = 0;
                foreach ($subscribers as $subscriber) {
                    if ($notificationsSent >= $this->sendLimit) {
                        break;
                    }
                    $hasApplied = array_search($subscriber->getEmail(), $applicationEmails) !== false;
                    $alreadyNotified = array_search($subscriber->getEmail(), $notificationEmails) !== false;
                    $subscribedMoreThanOneYearAgo = $subscriber->getTimestamp()->diff(new \DateTime())->y >= 1;
                    if ($hasApplied || $alreadyNotified || $subscribedMoreThanOneYearAgo) {
                        continue;
                    }

                    $this->sendAdmissionNotification($subscriber, $semester, $department);
                    ++$notificationsSent;
                }
                if ($notificationsSent > 0) {
                    $this->logger->info("*$notificationsSent* admission notification emails sent to subscribers in *".$department->getCity().'*');
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Failed to send admission notification:\n".$e->getMessage());
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function sendAdmissionNotification(AdmissionSubscriber $subscriber, Semester $semester, Department $department)
    {
        $this->emailSender->sendAdmissionStartedNotification($subscriber);
        $notification = new AdmissionNotification();
        $notification->setSemester($semester);
        $notification->setDepartment($department);
        $notification->setSubscriber($subscriber);
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function sendInfoMeetingNotifications()
    {
        $departments = $this->em->getRepository(Department::class)->findActive();
        $semester = $this->em->getRepository(Semester::class)->findOrCreateCurrentSemester();
        try {
            foreach ($departments as $department) {
                $admissionPeriod = $this->em->getRepository(AdmissionPeriod::class)->findOneByDepartmentAndSemester($department, $semester);

                if ($admissionPeriod === null || !$admissionPeriod->shouldSendInfoMeetingNotifications()) {
                    continue;
                }

                $applicationEmails = $this->em->getRepository(Application::class)->findEmailsByAdmissionPeriod($admissionPeriod);
                $subscribers = $this->em->getRepository(AdmissionSubscriber::class)->findByDepartment($department);
                $notificationEmails = $this->em->getRepository(AdmissionNotification::class)
                    ->findEmailsBySemesterAndDepartmentAndInfoMeeting($semester, $department);

                $notificationsSent = 0;
                foreach ($subscribers as $subscriber) {
                    if ($notificationsSent >= $this->sendLimit) {
                        break;
                    }
                    $hasApplied = array_search($subscriber->getEmail(), $applicationEmails) !== false;
                    $alreadyNotified = array_search($subscriber->getEmail(), $notificationEmails) !== false;
                    $subscribedMoreThanOneYearAgo = $subscriber->getTimestamp()->diff(new \DateTime())->y >= 1;
                    if ($hasApplied || $alreadyNotified || $subscribedMoreThanOneYearAgo || !$subscriber->getInfoMeeting()) {
                        continue;
                    }
                    $this->sendInfoMeetingNotification($subscriber, $semester, $department);
                    ++$notificationsSent;
                }
                if ($notificationsSent > 0) {
                    $this->logger->info("*$notificationsSent* info meeting notification emails sent to subscribers in *".$department->getCity().'*');
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical("Failed to send info meeting notification:\n".$e->getMessage());
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function sendInfoMeetingNotification(AdmissionSubscriber $subscriber, Semester $semester, Department $department)
    {
        $this->emailSender->sendInfoMeetingNotification($subscriber);
        $notification = new AdmissionNotification();
        $notification->setSemester($semester);
        $notification->setDepartment($department);
        $notification->setSubscriber($subscriber);
        $notification->setInfoMeeting(true);
        $this->em->persist($notification);
        $this->em->flush();
    }
}
