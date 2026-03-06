<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminSurveyDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyRepository $surveyRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        // Provider already verified existence and returns 404 if not found
        $id = $uriVariables['id'] ?? null;
        $survey = $this->surveyRepository->find($id);

        if ($survey !== null) {
            $this->em->remove($survey);
            $this->em->flush();
        }
    }
}
