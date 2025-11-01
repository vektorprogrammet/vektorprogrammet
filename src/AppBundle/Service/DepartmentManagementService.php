<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Service\Contract\DepartmentManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing departments and department-related business logic.
 */
class DepartmentManagementService implements DepartmentManagementServiceInterface
{
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createDepartment(Department $department): void
    {
        $this->entityManager->persist($department);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function updateDepartment(Department $department): void
    {
        $this->entityManager->persist($department);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDepartment(Department $department): void
    {
        $this->entityManager->remove($department);
        $this->entityManager->flush();
    }
}

