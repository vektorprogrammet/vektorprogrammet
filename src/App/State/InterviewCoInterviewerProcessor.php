<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Repository\InterviewRepository;
use App\Entity\User;
use App\Event\InterviewEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewCoInterviewerProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $interview = $id ? $this->interviewRepository->find($id) : null;

        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if ($data->userId !== null) {
            // Admin-assign path: intentionally skips interviewer/conducted checks,
            // matching the original adminAssignCoInterviewerAction controller behavior.
            $user = $this->em->getRepository(User::class)->find($data->userId);
            if (!$user) {
                throw new NotFoundHttpException('User not found.');
            }
            $interview->setCoInterviewer($user);
        // No event dispatch on admin-assign, matching original controller behavior.
        } else {
            // Self-assign path: enforce business rules from original assignCoInterviewerAction.
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();

            if ($interview->getUser() === $currentUser) {
                throw new BadRequestHttpException('Cannot be co-interviewer on your own interview.');
            }
            if ($interview->getInterviewer() === $currentUser) {
                throw new BadRequestHttpException('Cannot be both interviewer and co-interviewer.');
            }
            if ($interview->getInterviewed()) {
                throw new BadRequestHttpException('Cannot add co-interviewer to a conducted interview.');
            }

            $interview->setCoInterviewer($currentUser);
            $this->eventDispatcher->dispatch(new InterviewEvent($interview), InterviewEvent::COASSIGN);
        }

        $this->em->flush();
    }
}
