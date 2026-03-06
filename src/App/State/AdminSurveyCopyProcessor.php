<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdminSurveyCopyResource;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\SurveyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSurveyCopyProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyRepository $surveyRepository,
        private readonly SemesterRepository $semesterRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSurveyCopyResource
    {
        $id = $uriVariables['id'] ?? null;
        $survey = $id ? $this->surveyRepository->find($id) : null;

        if ($survey === null) {
            throw new NotFoundHttpException('Survey not found.');
        }

        $surveyClone = $survey->copy();

        $currentSemester = $this->semesterRepository->findOrCreateCurrentSemester();
        $surveyClone->setSemester($currentSemester);

        $this->em->persist($surveyClone);
        $this->em->flush();

        $result = new AdminSurveyCopyResource();
        $result->id = $surveyClone->getId();

        return $result;
    }
}
