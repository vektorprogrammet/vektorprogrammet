<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\School;
use AppBundle\Entity\User;
use AppBundle\Event\AssistantHistoryCreatedEvent;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\SchoolRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\SchoolManagementServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service for managing schools and school-related business logic.
 */
class SchoolManagementService implements SchoolManagementServiceInterface
{
    private $assistantHistoryRepository;
    private $departmentRepository;
    private $userRepository;
    private $schoolRepository;
    private $entityManager;
    private $eventDispatcher;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param SchoolRepositoryInterface $schoolRepository
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository,
        SchoolRepositoryInterface $schoolRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->schoolRepository = $schoolRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchoolsData(Department $department): array
    {
        $activeSchools = $this->schoolRepository->findActiveSchoolsByDepartment($department);
        $inactiveSchools = $this->schoolRepository->findInactiveSchoolsByDepartment($department);

        return [
            'activeSchools' => $activeSchools,
            'inactiveSchools' => $inactiveSchools,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAssistantHistoryData(School $school): array
    {
        $inactiveAssistantHistories = $this->assistantHistoryRepository->findInactiveAssistantHistoriesBySchool($school);
        $activeAssistantHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesBySchool($school);

        return [
            'activeAssistantHistories' => $activeAssistantHistories,
            'inactiveAssistantHistories' => $inactiveAssistantHistories,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersData(?Department $department, User $currentUser): array
    {
        $activeDepartments = $this->departmentRepository->findActive();

        // If department is provided, use it; otherwise get from current user's field of study
        if ($department === null) {
            $department = $currentUser->getFieldOfStudy()->getDepartment();
        }

        $users = $this->userRepository->findAllUsersByDepartment($department);

        return [
            'departments' => $activeDepartments,
            'department' => $department,
            'users' => $users,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createAssistantHistory(AssistantHistory $assistantHistory, User $user): void
    {
        $assistantHistory->setUser($user);
        $this->entityManager->persist($assistantHistory);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(AssistantHistoryCreatedEvent::NAME, new AssistantHistoryCreatedEvent($assistantHistory));
    }

    /**
     * {@inheritdoc}
     */
    public function createSchoolForDepartment(School $school, Department $department): void
    {
        $school->addDepartment($department);
        $department->addSchool($school);
        $this->entityManager->persist($school);
        $this->entityManager->persist($department);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSchool(School $school): void
    {
        $this->entityManager->remove($school);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeUserFromSchool(AssistantHistory $assistantHistory): void
    {
        $this->entityManager->remove($assistantHistory);
        $this->entityManager->flush();
    }
}

