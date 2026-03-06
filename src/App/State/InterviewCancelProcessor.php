<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Repository\InterviewRepository;
use App\Service\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InterviewCancelProcessor implements ProcessorInterface
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

        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if (!$interview->isPending()) {
            throw new UnprocessableEntityHttpException('Interview is not in pending state.');
        }

        $interview->setCancelMessage($data->cancelMessage);
        $interview->cancel();
        $this->em->persist($interview);
        $this->em->flush();

        $this->interviewManager->sendCancelEmail($interview);
    }
}
