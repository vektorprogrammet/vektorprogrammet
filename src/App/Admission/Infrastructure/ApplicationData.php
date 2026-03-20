<?php

namespace App\Admission\Infrastructure;

use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\Application;
use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\Department;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApplicationData
{
    private $department;
    private $admissionPeriod;
    private $applicationRepository;

    public function __construct(
        private readonly EntityManagerInterface $em,
        TokenStorageInterface $ts,
    ) {
        $this->applicationRepository = $this->em->getRepository(Application::class);

        if ($ts->getToken() !== null && $ts->getToken()->getUser() instanceof User) {
            $this->setDepartment($ts->getToken()->getUser()->getDepartment());
        }
    }

    public function setDepartment(Department $department)
    {
        $this->department = $department;
        $this->admissionPeriod = $department->getCurrentOrLatestAdmissionPeriod();
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
        return count($this->em->getRepository(Application::class)->findInterviewedApplicants($this->admissionPeriod));
    }

    public function getAssignedInterviewsCount(): int
    {
        return count($this->em->getRepository(Application::class)->findAssignedApplicants($this->admissionPeriod));
    }

    public function getTotalAssistantsCount(): int
    {
        return count($this->em->getRepository(AssistantHistory::class)->findByDepartmentAndSemester($this->department, $this->admissionPeriod->getSemester()));
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->em->getRepository(AssistantHistory::class)->findByDepartmentAndSemester($this->department, $this->admissionPeriod->getSemester());

        return $this->countPositions($assistantHistories, $this->getTotalAssistantsCount());
    }

    public function getTotalInterviewsCount(): int
    {
        return $this->getAssignedInterviewsCount() + $this->getInterviewedAssistantsCount();
    }

    public function applicantsNotYetInterviewedCount()
    {
        return $this->getCount() - $this->getCancelledInterviewsCount() - $this->getInterviewedAssistantsCount() - $this->getPreviousParticipationCount();
    }

    public function getInterviewsLeftCount(): int
    {
        return $this->getTotalInterviewsCount() - $this->getInterviewedAssistantsCount();
    }

    public function getFieldsOfStudyCounts(): array
    {
        $fieldOfStudyCount = [];
        $applicants = $this->applicationRepository->findBy(['admissionPeriod' => $this->admissionPeriod]);
        foreach ($applicants as $applicant) {
            $fieldOfStudyShortName = $applicant->getUser()->getFieldOfStudy()->getShortName();
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
        $studyYearCounts = [];
        $applicants = $this->applicationRepository->findBy(['admissionPeriod' => $this->admissionPeriod]);
        foreach ($applicants as $applicant) {
            $studyYear = $applicant->getYearOfStudy();
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
            if ($assistant->getBolk() === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }

    public function getAdmissionPeriod(): AdmissionPeriod
    {
        return $this->admissionPeriod;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function getHeardAboutFrom(): array
    {
        $heardAbout = [];
        $applicants = $this->applicationRepository->findBy(['admissionPeriod' => $this->admissionPeriod]);

        foreach ($applicants as $applicant) {
            $allHeardAboutFrom = $applicant->getHeardAboutFrom();

            if ($allHeardAboutFrom === null) {
                $allHeardAboutFrom = [0 => 'Ingen'];
            }

            for ($i = 0; $i < count($allHeardAboutFrom); ++$i) {
                $currentHeardAboutFrom = $allHeardAboutFrom[$i];

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
