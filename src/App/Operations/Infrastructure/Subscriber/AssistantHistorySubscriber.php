<?php

namespace App\Operations\Infrastructure\Subscriber;

use App\Shared\Entity\Semester;
use App\Operations\Domain\Events\AssistantHistoryCreatedEvent;
use App\Identity\Infrastructure\UserRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AssistantHistorySubscriber implements EventSubscriberInterface
{
    /**
     * ApplicationAdmissionSubscriber constructor.
     */
    public function __construct(private readonly RequestStack $requestStack, private readonly EntityManagerInterface $em, private readonly UserRegistration $userRegistrationService)
    {
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            AssistantHistoryCreatedEvent::NAME => [
                ['sendActivationMail', 0],
                ['addFlashMessage', -1],
            ],
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
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
            $currentSemester = $this->em->getRepository(Semester::class)
                ->findOrCreateCurrentSemester();

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

        $this->requestStack->getSession()->getFlashBag()->add('success', $message);
    }
}
