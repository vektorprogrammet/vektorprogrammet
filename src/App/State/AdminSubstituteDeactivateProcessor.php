<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdminSubstituteResource;
use App\Entity\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminSubstituteDeactivateProcessor implements ProcessorInterface
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

        $application->setSubstitute(false);

        $this->em->persist($application);
        $this->em->flush();

        $result = new AdminSubstituteResource();
        $result->id = $application->getId();

        return $result;
    }
}
