<?php

namespace App\Service;

use App\Entity\AssistantHistory;
use App\Entity\SurveyNotification;
use App\Entity\SurveyNotificationCollection;
use App\Entity\SurveyTaken;
use App\Support\Infrastructure\Mailer\MailerInterface;
use App\Support\Infrastructure\Sms\Sms;
use App\Support\Infrastructure\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class SurveyNotifier
{
    public function __construct(
        private readonly string $fromEmail,
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $em,
        private readonly RouterInterface $router,
        private readonly SmsSenderInterface $smsSender,
    ) {
    }

    public function initializeSurveyNotifier(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $userGroups = $surveyNotificationCollection->getUserGroups();

        foreach ($userGroups as $userGroup) {
            $userGroup->setActive(true);
            $userGroupCollection = $userGroup->getUserGroupCollection();
            $userGroupCollection->setDeletable(false);
            $this->em->persist($userGroup);
        }

        $this->em->persist($surveyNotificationCollection);
        $this->em->flush();
    }

    private function createSurveyNotifications(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isActive()) {
            return;
        }

        $survey = $surveyNotificationCollection->getSurvey();

        $users = $this->findUsers($surveyNotificationCollection);
        foreach ($users as $user) {
            $isSurveyTakenByUser = !empty($this->em->getRepository(SurveyTaken::class)->findAllBySurveyAndUser($survey, $user));
            if ($isSurveyTakenByUser) {
                continue;
            }

            $notification = new SurveyNotification();
            $notification->setUser($user);
            $this->ensureUniqueIdentifier($notification);
            $notification->setSurveyNotificationCollection($surveyNotificationCollection);
            $this->em->persist($notification);
            $this->em->flush();
        }
    }

    public function sendNotifications(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $this->createSurveyNotifications($surveyNotificationCollection);
        $surveyNotificationCollection->setActive(true);
        $this->isAllSent($surveyNotificationCollection);
        $this->em->persist($surveyNotificationCollection);
        $this->em->flush();

        if ($surveyNotificationCollection->isAllSent()) {
            return;
        } elseif ($surveyNotificationCollection->getNotificationType() === SurveyNotificationCollection::$SMS_NOTIFICATION) {
            $this->sendSMS($surveyNotificationCollection);
        } elseif ($surveyNotificationCollection->getNotificationType() === SurveyNotificationCollection::$EMAIL_NOTIFICATION) {
            $this->sendEmail($surveyNotificationCollection);
        }
    }

    private function sendSMS(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $numSmsSent = 0;
        $customMessage = $surveyNotificationCollection->getSmsMessage();
        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $notification->setTimeNotificationSent(new \DateTime());
            $this->em->persist($notification);

            $user = $notification->getUser();
            $phoneNumber = $user->getPhone();
            $validNumber = $this->smsSender->validatePhoneNumber($phoneNumber);
            if (!$validNumber) {
                $this->logger->alert("Kunne ikke sende schedule sms til *$user*\n Tlf.nr.: *$phoneNumber*");
                continue;
            }

            $message =
                'Hei, '.$notification->getUser()->getFirstName()."\n".
                $customMessage;

            $sms = new Sms();
            $sms->setMessage($message);
            $sms->setSender('Vektor');
            $sms->setRecipients([$phoneNumber]);
            $this->smsSender->send($sms);
            ++$numSmsSent;
            $this->em->flush();
        }

        $this->logger->info("*$numSmsSent* notifications SMSs' sent about survey");
        $this->isAllSent($surveyNotificationCollection);
    }

    private function sendEmail(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $numEmailSent = 0;
        $surveyId = $surveyNotificationCollection->getSurvey()->getId();
        $mainMessage = $surveyNotificationCollection->getEmailMessage();
        $subject = $surveyNotificationCollection->getEmailSubject();
        $emailMessage = $surveyNotificationCollection->getEmailEndMessage();
        $emailType = $surveyNotificationCollection->getEmailType();
        $emailFromName = $surveyNotificationCollection->getEmailFromName();

        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if ($notification->isSent()) {
                return;
            }
            $notification->setSent(true);
            $notification->setTimeNotificationSent(new \DateTime());
            $this->em->persist($notification);

            $user = $notification->getUser();
            $identifier = $notification->getUserIdentifier();
            $email = $user->getEmail();

            if ($emailType === 1) {
                $assistantHistory = $this->em->getRepository(AssistantHistory::class)->findMostRecentByUser($user);
                if (empty($assistantHistory)) {
                    continue;
                }
                $assistantHistory = $assistantHistory[0];
                $day = $assistantHistory->getDay();
                $school = $assistantHistory->getSchool()->getName();

                $content = $this->twig->render(
                    'survey/default_assistant_survey_notification_email.html.twig',
                    [
                        'firstname' => $user->getFirstName(),
                        'route' => $this->router->generate('survey_show_user_id', ['id' => $surveyId, 'userid' => $identifier], RouterInterface::ABSOLUTE_URL),
                        'day' => $day,
                        'school' => $school,
                        'fromName' => $surveyNotificationCollection->getEmailFromName(),
                        'title' => $subject,
                    ]
                );
            } elseif ($emailType === 2) {
                $assistantHistory = $this->em->getRepository(AssistantHistory::class)->findMostRecentByUser($user);
                if (empty($assistantHistory)) {
                    continue;
                }
                $assistantHistory = $assistantHistory[0];
                $day = $assistantHistory->getDay();
                $school = $assistantHistory->getSchool()->getName();

                $subject = 'Hvordan var det på '.$school.'?';

                $content = $this->twig->render(
                    'survey/personal_email_notification.html.twig',
                    [
                        'firstname' => $user->getFirstName(),
                        'route' => $this->router->generate('survey_show_user_id', ['id' => $surveyId, 'userid' => $identifier], RouterInterface::ABSOLUTE_URL),
                        'day' => $day,
                        'school' => $school,
                        'fromName' => $surveyNotificationCollection->getEmailFromName(),
                        'title' => $subject,
                    ]
                );
            } else {
                $content = $this->twig->render(
                    'survey/email_notification.html.twig',
                    [
                        'firstname' => $user->getFirstName(),
                        'route' => $this->router->generate('survey_show_user_id', ['id' => $surveyId, 'userid' => $identifier], RouterInterface::ABSOLUTE_URL),
                        'mainMessage' => $mainMessage,
                        'endMessage' => $emailMessage,
                        'fromName' => $surveyNotificationCollection->getEmailFromName(),
                        'title' => $subject,
                    ]
                );
            }

            $message = (new Email())
                ->from(new Address($this->fromEmail, $emailFromName))
                ->subject($subject)
                ->to($email)
                ->replyTo($this->fromEmail)
                ->html($content);
            $this->mailer->send($message);
            ++$numEmailSent;
            $this->em->flush();
        }
        $this->logger->info("*$numEmailSent* notifications emails sent about survey");
        $this->isAllSent($surveyNotificationCollection);
    }

    private function isAllSent(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isAllSent()) {
            return true;
        } elseif (empty($surveyNotificationCollection->getSurveyNotifications())) {
            return false;
        }

        foreach ($surveyNotificationCollection->getSurveyNotifications() as $notification) {
            if (!$notification->isSent()) {
                return false;
            }
        }

        $surveyNotificationCollection->setAllSent(true);
        $this->em->persist($surveyNotificationCollection);
        $this->em->flush();

        return true;
    }

    private function findUsers(SurveyNotificationCollection $surveyNotificationCollection)
    {
        $userGroups = $surveyNotificationCollection->getUserGroups();
        $users = [];
        foreach ($userGroups as $userGroup) {
            $userGroup->setActive(true);
            $this->em->persist($userGroup);

            $userGroupUsers = $userGroup->getUsers();
            foreach ($userGroupUsers as $user) {
                $users[] = $user;
            }
        }
        $users = array_unique($users, SORT_REGULAR);

        $this->em->flush();

        return $users;
    }

    private function ensureUniqueIdentifier(SurveyNotification $notification)
    {
        while ($this->em->getRepository(SurveyNotification::class)->findByUserIdentifier($notification->getUserIdentifier())) {
            $notification->setUserIdentifier(bin2hex(openssl_random_pseudo_bytes(12)));
        }
    }
}
