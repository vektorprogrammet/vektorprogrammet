<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\InterviewBulkAssignInput;
use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewBulkAssignProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InterviewManager $interviewManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewBulkAssignInput);

        foreach ($data->assignments as $assignment) {
            $application = $this->em->getRepository(Application::class)->find($assignment['applicationId']);
            if (!$application) {
                throw new NotFoundHttpException('Application not found: '.$assignment['applicationId']);
            }

            $interviewer = $this->em->getRepository(User::class)->find($assignment['interviewerId']);
            if (!$interviewer) {
                throw new NotFoundHttpException('Interviewer not found: '.$assignment['interviewerId']);
            }

            $schema = $this->em->getRepository(InterviewSchema::class)->find($assignment['interviewSchemaId']);
            if (!$schema) {
                throw new NotFoundHttpException('Interview schema not found: '.$assignment['interviewSchemaId']);
            }

            $this->interviewManager->assignInterviewerToApplication($interviewer, $application);

            $interview = $application->getInterview();
            $interview->setInterviewSchema($schema);

            $this->em->persist($interview);
        }

        $this->em->flush();
    }
}
