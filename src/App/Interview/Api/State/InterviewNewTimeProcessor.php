<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Infrastructure\Repository\InterviewRepository;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InterviewNewTimeProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
        private readonly InterviewManager $interviewManager,
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

        if ($data->newTimeMessage === null || $data->newTimeMessage === '') {
            throw new UnprocessableEntityHttpException('A message is required when requesting a new time.');
        }

        $interview->setNewTimeMessage($data->newTimeMessage);
        $interview->requestNewTime();
        $this->em->persist($interview);
        $this->em->flush();

        $this->interviewManager->sendRescheduleEmail($interview);
    }
}
