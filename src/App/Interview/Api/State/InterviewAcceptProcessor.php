<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InterviewAcceptProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $responseCode = $uriVariables['responseCode'] ?? '';
        $interview = $this->interviewRepository->findByResponseCode($responseCode);

        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if (!$interview->isPending()) {
            throw new UnprocessableEntityHttpException('Interview is not in pending state.');
        }

        $interview->acceptInterview();
        $this->em->persist($interview);
        $this->em->flush();
    }
}
