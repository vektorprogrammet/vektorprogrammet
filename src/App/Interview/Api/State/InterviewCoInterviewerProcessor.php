<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Domain\Events\InterviewEvent;
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
        $interview = $id !== null ? $this->interviewRepository->find($id) : null;

        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if ($data->userId !== null) {
            // Admin-assign path: intentionally skips interviewer/conducted checks,
            // matching the original adminAssignCoInterviewerAction controller behavior.
            $user = $this->em->getRepository(User::class)->find($data->userId);
            if ($user === null) {
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
