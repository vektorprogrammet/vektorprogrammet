<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Repository\InterviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InterviewDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly InterviewRepository $interviewRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $interview = $id ? $this->interviewRepository->find($id) : null;

        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        $application = $interview->getApplication();
        if ($application) {
            $application->setInterview(null);
        }

        $this->em->remove($interview);
        $this->em->flush();
    }
}
