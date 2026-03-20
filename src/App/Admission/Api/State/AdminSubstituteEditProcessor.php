<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Api\Resource\AdminSubstituteResource;
use App\Admission\Infrastructure\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSubstituteEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ApplicationRepository $applicationRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminSubstituteResource
    {
        $id = $uriVariables['id'] ?? null;
        $application = $id ? $this->applicationRepo->find($id) : null;

        if ($application === null) {
            throw new NotFoundHttpException('Application not found.');
        }

        if (!$application->isSubstitute()) {
            throw new BadRequestHttpException('Application is not a substitute.');
        }

        // Update fields from the DTO
        if ($data->monday !== null) {
            $application->setMonday($data->monday);
        }
        if ($data->tuesday !== null) {
            $application->setTuesday($data->tuesday);
        }
        if ($data->wednesday !== null) {
            $application->setWednesday($data->wednesday);
        }
        if ($data->thursday !== null) {
            $application->setThursday($data->thursday);
        }
        if ($data->friday !== null) {
            $application->setFriday($data->friday);
        }
        if ($data->yearOfStudy !== null) {
            $application->setYearOfStudy($data->yearOfStudy);
        }
        if ($data->language !== null) {
            $application->setLanguage($data->language);
        }

        $this->em->persist($application);
        $this->em->flush();

        $result = new AdminSubstituteResource();
        $result->id = $application->getId();

        return $result;
    }
}
