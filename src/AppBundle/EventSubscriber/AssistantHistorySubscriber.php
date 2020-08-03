<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\AssistantHistoryCreatedEvent;
use AppBundle\Service\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AssistantHistorySubscriber implements EventSubscriberInterface
{
    private $session;
    private $em;
    private $userRegistrationService;

    /**
     * ApplicationAdmissionSubscriber constructor.
     *
     * @param Session          $session
     * @param EntityManagerInterface    $em
     * @param UserRegistration $userRegistrationService
     */
    public function __construct(Session $session, EntityManagerInterface $em, UserRegistration $userRegistrationService)
    {
        $this->session = $session;
        $this->em = $em;
        $this->userRegistrationService = $userRegistrationService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            AssistantHistoryCreatedEvent::NAME => array(
                array('sendActivationMail', 0),
                array('addFlashMessage', -1),
            ),
        );
    }

    /**
     * @param AssistantHistoryCreatedEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendActivationMail(AssistantHistoryCreatedEvent $event)
    {
        $assistantHistory = $event->getAssistantHistory();
        $user = $assistantHistory->getUser();

        // Check if user already has user name and password
        if ($user->getUserName() !== null && $user->getPassword() !== null) {
            $user->setActive(true);
            $this->em->persist($user);
            $this->em->flush();
        } else { // Send new user code for user to create user name and password
            $currentSemester = $this->em->getRepository('AppBundle:Semester')
                ->findCurrentSemester();

            // Send new user code only if assistant history is added to current semester
            if ($assistantHistory->getSemester() === $currentSemester && $user->getNewUserCode() === null) {
                $this->userRegistrationService->sendActivationCode($user);
            }
        }
    }

    public function addFlashMessage(AssistantHistoryCreatedEvent $event)
    {
        $assistantHistory = $event->getAssistantHistory();
        $message = "{$assistantHistory->getUser()} har blitt fordelt til {$assistantHistory->getSchool()}.";

        $this->session->getFlashBag()->add('success', $message);
    }
}
