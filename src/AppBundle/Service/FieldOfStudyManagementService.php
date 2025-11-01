<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Repository\Contract\FieldOfStudyRepositoryInterface;
use AppBundle\Service\Contract\FieldOfStudyManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing fields of study and field of study-related business logic.
 */
class FieldOfStudyManagementService implements FieldOfStudyManagementServiceInterface
{
    private $fieldOfStudyRepository;
    private $entityManager;

    /**
     * @param FieldOfStudyRepositoryInterface $fieldOfStudyRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        FieldOfStudyRepositoryInterface $fieldOfStudyRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->fieldOfStudyRepository = $fieldOfStudyRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsOfStudyByDepartment(Department $department): array
    {
        return $this->fieldOfStudyRepository->findByDepartment($department);
    }

    /**
     * {@inheritdoc}
     */
    public function saveFieldOfStudy(FieldOfStudy $fieldOfStudy, Department $department): void
    {
        $fieldOfStudy->setDepartment($department);
        $this->entityManager->persist($fieldOfStudy);
        $this->entityManager->flush();
    }
}

