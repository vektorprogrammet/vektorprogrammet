<?php

namespace App\Service;

use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\Semester;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\SemesterRepositoryInterface;
use App\Service\Contract\AssistantHistoryDataInterface;
use App\Service\Contract\GeoLocationInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssistantHistoryData implements AssistantHistoryDataInterface
{
    private AssistantHistoryRepositoryInterface $assistantHistoryRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private SemesterRepositoryInterface $semesterRepository;
    private ?Semester $semester;
    private ?Department $department;

    public function __construct(
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        DepartmentRepositoryInterface $departmentRepository,
        SemesterRepositoryInterface $semesterRepository,
        TokenStorageInterface $ts,
        GeoLocationInterface $geoLocation
    ) {
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->departmentRepository = $departmentRepository;
        $this->semesterRepository = $semesterRepository;
        
        $token = $ts->getToken();
        $user = $token ? $token->getUser() : null;
        
        $departments = $this->departmentRepository->findAll();
        if ($user == "anon." || !$user) {
            $this->department = $geoLocation->findNearestDepartment($departments);
        } else {
            $this->department = $user->fieldOfStudy->department ?? null;
        }
        $this->semester = $this->semesterRepository->findOrCreateCurrentSemester();
    }

    /**
     * @param Semester $semester
     *
     * @return AssistantHistoryDataInterface
     */
    public function setSemester(Semester $semester): AssistantHistoryDataInterface
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @param Department $department
     *
     * @return AssistantHistoryData
     */
    public function setDepartment(Department $department): AssistantHistoryDataInterface
    {
        $this->department = $department;
        return $this;
    }

    public function getAssistantHistoryCount(): int
    {
        return count($this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->semester));
    }

    public function getCount(): int
    {
        return $this->getAssistantHistoryCount();
    }

    public function getMaleCount(): int
    {
        return $this->assistantHistoryRepository->numMaleBySemester($this->semester);
    }

    public function getFemaleCount(): int
    {
        return $this->assistantHistoryRepository->numFemaleBySemester($this->semester);
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->semester);
        $positionsCount = count($assistantHistories);
        foreach ($assistantHistories as $assistant) {
            if (($assistant->bolk ?? '') === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }
}
