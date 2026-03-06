<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminAdmissionPeriodDeleteResource;
use App\Entity\AdmissionPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminAdmissionPeriodDeleteProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminAdmissionPeriodDeleteResource
    {
        $id = $uriVariables['id'] ?? null;
        $admissionPeriod = $id ? $this->em->getRepository(AdmissionPeriod::class)->find($id) : null;

        if ($admissionPeriod === null) {
            throw new NotFoundHttpException('Admission period not found.');
        }

        $resource = new AdminAdmissionPeriodDeleteResource();
        $resource->id = $admissionPeriod->getId();

        return $resource;
    }
}
