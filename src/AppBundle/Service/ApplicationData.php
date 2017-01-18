<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\ApplicationRepository;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ApplicationData
{
    /**
     * @var Department
     */
    private $department;
    /**
     * @var Semester
     */
    private $semester;
    /**
     * @var ApplicationRepository
     */
    private $applicationRepository;
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * ApplicationData constructor.
     *
     * @param EntityManager $em
     * @param TokenStorage  $ts
     */
    public function __construct(EntityManager $em, TokenStorage $ts)
    {
        $this->em = $em;
        $this->applicationRepository = $this->em->getRepository('AppBundle:Application');

        if ($ts->getToken() !== null && $ts->getToken()->getUser() instanceof User) {
            $this->setDepartment($ts->getToken()->getUser()->getDepartment());
        }
    }

    public function setDepartment(Department $department)
    {
        $this->department = $department;
        $this->semester = $this->em->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
    }

    public function setSemester(Semester $semester)
    {
        $this->semester = $semester;
    }

    public function getApplicationCount(): int
    {
        return $this->applicationRepository->numOfApplications($this->semester);
    }

    public function getCount(): int
    {
        return $this->getApplicationCount();
    }

    public function getMaleCount(): int
    {
        return $this->applicationRepository->numOfGender($this->semester, 0);
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
        return $this->applicationRepository->numOfGender($this->semester, 1);
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
        return $this->applicationRepository->numOfPreviousParticipation($this->semester);
    }

    public function getCancelledInterviewsCount(): int
    {
        return count($this->applicationRepository->findCancelledApplicants($this->semester));
    }

    public function getInterviewedAssistantsCount(): int
    {
        return count($this->em->getRepository('AppBundle:Application')->findInterviewedApplicants($this->department, $this->semester));
    }

    public function getAssignedInterviewsCount(): int
    {
        return count($this->em->getRepository('AppBundle:Application')->findAssignedApplicants($this->department, $this->semester));
    }

    public function getTotalAssistantsCount(): int
    {
        return count($this->em->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($this->department, $this->semester));
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->em->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($this->department, $this->semester);

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
        $fieldOfStudyCount = array();
        $applicants = $this->applicationRepository->findBy(array('semester' => $this->semester));
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
        $studyYearCounts = array();
        $applicants = $this->applicationRepository->findBy(array('semester' => $this->semester));
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

    /**
     * @return Semester
     */
    public function getSemester(): Semester
    {
        return $this->semester;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }
}
