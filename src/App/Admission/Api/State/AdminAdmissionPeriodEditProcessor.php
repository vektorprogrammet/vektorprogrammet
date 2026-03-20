<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminAdmissionPeriodEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $id = $uriVariables['id'] ?? null;
        $admissionPeriod = $this->em->getRepository(AdmissionPeriod::class)->find($id);

        if ($admissionPeriod === null) {
            throw new NotFoundHttpException('Admission period not found.');
        }

        $admissionPeriod->setStartDate(new \DateTime($data->startDate));
        $admissionPeriod->setEndDate(new \DateTime($data->endDate));

        $this->em->persist($admissionPeriod);
        $this->em->flush();

        return ['id' => $admissionPeriod->getId()];
    }
}
