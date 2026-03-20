<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewClearCoInterviewerProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $interview = $id !== null ? $this->interviewRepository->find($id) : null;

        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        $interview->setCoInterviewer(null);
        $this->em->flush();
    }
}
