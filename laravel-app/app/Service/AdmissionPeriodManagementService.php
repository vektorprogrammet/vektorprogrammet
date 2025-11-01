<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Department;
use App\Service\Contract\AdmissionPeriodManagementServiceInterface;
use App\Service\Contract\AdmissionPeriodValidationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing admission periods and admission period-related business logic.
 */
class AdmissionPeriodManagementService implements AdmissionPeriodManagementServiceInterface
{
    private $entityManager;
    private $admissionPeriodValidationService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AdmissionPeriodValidationServiceInterface $admissionPeriodValidationService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AdmissionPeriodValidationServiceInterface $admissionPeriodValidationService
    ) {
        $this->entityManager = $entityManager;
        $this->admissionPeriodValidationService = $admissionPeriodValidationService;
    }

    /**
     * {@inheritdoc}
     */
    public function createAdmissionPeriod(AdmissionPeriod $admissionPeriod, Department $department): array
    {
        $exists = $this->admissionPeriodValidationService->semesterExistsForDepartment($admissionPeriod, $department);

        if ($exists) {
            return [
                'success' => false,
                'exists' => true,
            ];
        }

        $admissionPeriod->setDepartment($department);
        $this->entityManager->persist($admissionPeriod);
        $this->entityManager->flush();

        return [
            'success' => true,
            'exists' => false,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateAdmissionPeriod(AdmissionPeriod $admissionPeriod): void
    {
        $this->entityManager->persist($admissionPeriod);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAdmissionPeriod(AdmissionPeriod $admissionPeriod): void
    {
        $infoMeeting = $admissionPeriod->getInfoMeeting();
        if ($infoMeeting !== null) {
            $this->entityManager->remove($infoMeeting);
        }
        $this->entityManager->remove($admissionPeriod);
        $this->entityManager->flush();
    }
}

