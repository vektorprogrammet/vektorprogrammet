<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Admission\Api\Resource\AdminAdmissionPeriodWriteResource;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminAdmissionPeriodEditProvider implements ProviderInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): AdminAdmissionPeriodWriteResource
    {
        $id = $uriVariables['id'] ?? null;
        $admissionPeriod = $id ? $this->em->getRepository(AdmissionPeriod::class)->find($id) : null;

        if ($admissionPeriod === null) {
            throw new NotFoundHttpException('Admission period not found.');
        }

        $resource = new AdminAdmissionPeriodWriteResource();
        $resource->id = $admissionPeriod->getId();
        $resource->departmentId = $admissionPeriod->getDepartment()?->getId();
        $resource->semesterId = $admissionPeriod->getSemester()?->getId();
        $resource->startDate = $admissionPeriod->getStartDate()?->format('Y-m-d');
        $resource->endDate = $admissionPeriod->getEndDate()?->format('Y-m-d');

        return $resource;
    }
}
