<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\School;
use App\Models\User;
use App\Event\AssistantHistoryCreatedEvent;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\SchoolRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Service\Contract\SchoolManagementServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service for managing schools and school-related business logic.
 */
class SchoolManagementService implements SchoolManagementServiceInterface
{
    private AssistantHistoryRepositoryInterface $assistantHistoryRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private UserRepositoryInterface $userRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param SchoolRepositoryInterface $schoolRepository
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository,
        SchoolRepositoryInterface $schoolRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->schoolRepository = $schoolRepository;
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
            $department = $currentUser->fieldOfStudy->department;
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
        $assistantHistory->user_id = $user->id;
        $assistantHistory->save();

        $this->eventDispatcher->dispatch(AssistantHistoryCreatedEvent::NAME, new AssistantHistoryCreatedEvent($assistantHistory));
    }

    /**
     * {@inheritdoc}
     */
    public function createSchoolForDepartment(School $school, Department $department): void
    {
        $school->departments()->attach($department->id);
        $school->save();
        $department->save();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSchool(School $school): void
    {
        $school->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function removeUserFromSchool(AssistantHistory $assistantHistory): void
    {
        $assistantHistory->delete();
    }
}

