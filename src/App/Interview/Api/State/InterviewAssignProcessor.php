<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\InterviewAssignInput;
use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewAssignProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InterviewManager $interviewManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewAssignInput);

        $application = $this->em->getRepository(Application::class)->find($data->applicationId);
        if ($application === null) {
            throw new NotFoundHttpException('Application not found.');
        }

        $interviewer = $this->em->getRepository(User::class)->find($data->interviewerId);
        if ($interviewer === null) {
            throw new NotFoundHttpException('Interviewer not found.');
        }

        $schema = $this->em->getRepository(InterviewSchema::class)->find($data->interviewSchemaId);
        if ($schema === null) {
            throw new NotFoundHttpException('Interview schema not found.');
        }

        $this->interviewManager->assignInterviewerToApplication($interviewer, $application);

        /** @var \App\Interview\Infrastructure\Entity\Interview $interview */
        $interview = $application->getInterview();
        $interview->setInterviewSchema($schema);

        $this->em->persist($interview);
        $this->em->flush();
    }
}
