<?php

namespace App\Operations\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Scheduling\Infrastructure\Entity\School;
use App\Shared\Entity\Semester;
use App\Entity\User;
use App\Operations\Domain\Events\AssistantHistoryCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminAssistantHistoryCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $schoolId = $uriVariables['id'] ?? $data->schoolId;
        $school = $this->em->getRepository(School::class)->find($schoolId);

        if ($school === null) {
            throw new UnprocessableEntityHttpException('School not found.');
        }

        // Department-scoping: non-team-leaders can only delegate within their own department
        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User && !$this->security->isGranted('ROLE_ADMIN')) {
            $currentDept = $currentUser->getFieldOfStudy()?->getDepartment();
            if ($currentDept !== null) {
                $schoolBelongsToDept = $school->getDepartments()->exists(
                    fn ($key, $dept) => $dept->getId() === $currentDept->getId()
                );
                if (!$schoolBelongsToDept) {
                    throw new AccessDeniedHttpException('You can only delegate within your own department.');
                }
            }
        }

        $user = $data->userId ? $this->em->getRepository(User::class)->find($data->userId) : null;
        if ($user === null) {
            throw new UnprocessableEntityHttpException('User not found.');
        }

        $semester = $data->semesterId ? $this->em->getRepository(Semester::class)->find($data->semesterId) : null;
        if ($semester === null) {
            throw new UnprocessableEntityHttpException('Semester not found.');
        }

        $department = $user->getDepartment();

        $assistantHistory = new AssistantHistory();
        $assistantHistory->setUser($user);
        $assistantHistory->setSchool($school);
        $assistantHistory->setSemester($semester);
        $assistantHistory->setDepartment($department);
        $assistantHistory->setWorkdays($data->workdays);
        $assistantHistory->setBolk($data->bolk);
        $assistantHistory->setDay($data->day);

        $this->em->persist($assistantHistory);
        $this->em->flush();

        try {
            $this->eventDispatcher->dispatch(
                new AssistantHistoryCreatedEvent($assistantHistory),
                AssistantHistoryCreatedEvent::NAME,
            );
        } catch (\Throwable $e) {
            $this->logger->error('AssistantHistoryCreatedEvent handler failed: '.$e->getMessage());
        }

        return ['id' => $assistantHistory->getId()];
    }
}
