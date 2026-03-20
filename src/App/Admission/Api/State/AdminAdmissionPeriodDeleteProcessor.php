<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use Doctrine\ORM\EntityManagerInterface;

class AdminAdmissionPeriodDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $id = $uriVariables['id'] ?? null;
        $admissionPeriod = $this->em->getRepository(AdmissionPeriod::class)->find($id);

        if ($admissionPeriod !== null) {
            // Remove linked InfoMeeting first (foreign key constraint)
            $infoMeeting = $admissionPeriod->getInfoMeeting();
            if ($infoMeeting !== null) {
                $this->em->remove($infoMeeting);
            }

            $this->em->remove($admissionPeriod);
            $this->em->flush();
        }
    }
}
