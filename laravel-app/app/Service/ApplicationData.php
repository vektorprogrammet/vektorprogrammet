<?php

namespace App\Service;

use App\Models\AdmissionPeriod;
use App\Models\Application;
use App\Models\AssistantHistory;
use App\Models\Department;
use App\Models\User;
use App\Repository\Contract\ApplicationRepositoryInterface;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Service\Contract\ApplicationDataInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApplicationData implements ApplicationDataInterface
{
    private ?Department $department;
    private ?AdmissionPeriod $admissionPeriod;
    private ApplicationRepositoryInterface $applicationRepository;
    private AssistantHistoryRepositoryInterface $assistantHistoryRepository;

    /**
     * ApplicationData constructor.
     *
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param TokenStorageInterface $ts
     */
    public function __construct(
        ApplicationRepositoryInterface $applicationRepository,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        TokenStorageInterface $ts
    ) {
        $this->applicationRepository = $applicationRepository;
        $this->assistantHistoryRepository = $assistantHistoryRepository;

        $token = $ts->getToken();
        if ($token !== null && $token->getUser() instanceof User) {
            $user = $token->getUser();
            $this->setDepartment($user->fieldOfStudy->department ?? null);
        }
    }

    public function setDepartment(?Department $department): void
    {
        $this->department = $department;
        if ($department !== null) {
            // Note: getCurrentOrLatestAdmissionPeriod() method needs to be implemented on Department model
            $this->admissionPeriod = $department->currentOrLatestAdmissionPeriod ?? null;
        } else {
            $this->admissionPeriod = null;
        }
    }

    public function setAdmissionPeriod(AdmissionPeriod $admissionPeriod)
    {
        $this->admissionPeriod = $admissionPeriod;
    }

    public function getApplicationCount(): int
    {
        if (!$this->admissionPeriod) {
            return 0;
        }

        return $this->applicationRepository->numOfApplications($this->admissionPeriod);
    }


    public function getCount(): int
    {
        return $this->getApplicationCount();
    }

    public function getMaleCount(): int
    {
        return $this->applicationRepository->numOfGender($this->admissionPeriod, 0);
    }

    public function getMalePercentage(): float
    {
        if ($this->getApplicationCount() === 0) {
            return floatval(0);
        }

        return round(100 * floatval($this->getMaleCount() / $this->getApplicationCount()), 2);
    }

    public function getFemaleCount(): int
    {
        return $this->applicationRepository->numOfGender($this->admissionPeriod, 1);
    }

    public function getFemalePercentage(): float
    {
        if ($this->getApplicationCount() === 0) {
            return floatval(0);
        }

        return round(100 * floatval($this->getFemaleCount() / $this->getApplicationCount()), 2);
    }

    public function getPreviousParticipationCount(): int
    {
        return $this->applicationRepository->numOfPreviousParticipation($this->admissionPeriod);
    }

    public function getCancelledInterviewsCount(): int
    {
        return count($this->applicationRepository->findCancelledApplicants($this->admissionPeriod));
    }

    public function getInterviewedAssistantsCount(): int
    {
        return count($this->applicationRepository->findInterviewedApplicants($this->admissionPeriod));
    }

    public function getAssignedInterviewsCount(): int
    {
        return count($this->applicationRepository->findAssignedApplicants($this->admissionPeriod));
    }

    public function getTotalAssistantsCount(): int
    {
        if (!$this->admissionPeriod || !$this->department) {
            return 0;
        }
        return count($this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->admissionPeriod->semester));
    }

    public function getPositionsCount(): int
    {
        if (!$this->admissionPeriod || !$this->department) {
            return 0;
        }
        $assistantHistories = $this->assistantHistoryRepository->findByDepartmentAndSemester($this->department, $this->admissionPeriod->semester);

        return $this->countPositions($assistantHistories, $this->getTotalAssistantsCount());
    }

    public function getTotalInterviewsCount(): int
    {
        return $this->getAssignedInterviewsCount() + $this->getInterviewedAssistantsCount();
    }

    public function applicantsNotYetInterviewedCount(): int
    {
        return $this->getCount() - $this->getCancelledInterviewsCount() - $this->getInterviewedAssistantsCount() - $this->getPreviousParticipationCount();
    }

    public function getInterviewsLeftCount(): int
    {
        return $this->getTotalInterviewsCount() - $this->getInterviewedAssistantsCount();
    }

    public function getFieldsOfStudyCounts(): array
    {
        if (!$this->admissionPeriod) {
            return [];
        }
        $fieldOfStudyCount = [];
        $applicants = $this->applicationRepository->findByAdmissionPeriod($this->admissionPeriod);
        foreach ($applicants as $applicant) {
            $fieldOfStudyShortName = $applicant->user->fieldOfStudy->short_name ?? 'Unknown';
            if (array_key_exists($fieldOfStudyShortName, $fieldOfStudyCount)) {
                ++$fieldOfStudyCount[$fieldOfStudyShortName];
            } else {
                $fieldOfStudyCount[$fieldOfStudyShortName] = 1;
            }
        }
        ksort($fieldOfStudyCount);

        return $fieldOfStudyCount;
    }

    public function getStudyYearCounts(): array
    {
        if (!$this->admissionPeriod) {
            return [];
        }
        $studyYearCounts = [];
        $applicants = $this->applicationRepository->findByAdmissionPeriod($this->admissionPeriod);
        foreach ($applicants as $applicant) {
            $studyYear = $applicant->year_of_study ?? 0;
            if (array_key_exists($studyYear, $studyYearCounts)) {
                ++$studyYearCounts[$studyYear];
            } else {
                $studyYearCounts[$studyYear] = 1;
            }
        }
        ksort($studyYearCounts);

        return $studyYearCounts;
    }

    private function countPositions(array $assistantHistories, int $totalAssistantsCount): int
    {
        $positionsCount = $totalAssistantsCount;
        foreach ($assistantHistories as $assistant) {
            if (($assistant->bolk ?? '') === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }

    /**
     * @return AdmissionPeriod
     */
    public function getAdmissionPeriod(): AdmissionPeriod
    {
        return $this->admissionPeriod;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function getHeardAboutFrom(): array
    {
        if (!$this->admissionPeriod) {
            return [];
        }
        $heardAbout = [];
        $applicants = $this->applicationRepository->findByAdmissionPeriod($this->admissionPeriod);

        foreach ($applicants as $applicant) {
            $allHeardAboutFrom = $applicant->heard_about_from ?? null;

            if ($allHeardAboutFrom === null) {
                $allHeardAboutFrom = [0 => "Ingen"];
            } elseif (!is_array($allHeardAboutFrom)) {
                $allHeardAboutFrom = [$allHeardAboutFrom];
            }

            foreach ($allHeardAboutFrom as $currentHeardAboutFrom) {
                if (array_key_exists($currentHeardAboutFrom, $heardAbout)) {
                    ++$heardAbout[$currentHeardAboutFrom];
                } else {
                    $heardAbout[$currentHeardAboutFrom] = 1;
                }
            }
        }
        return $heardAbout;
    }
}
