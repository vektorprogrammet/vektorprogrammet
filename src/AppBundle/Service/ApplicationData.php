<?php

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ApplicationData
{
    private $applicationRepository;
    private $semester;
    private $em;

    public function __construct(EntityManager $em, TokenStorage $ts)
    {
        $this->applicationRepository = $em->getRepository('AppBundle:Application');
        if ($ts->getToken() !== null && $ts->getToken()->getUser() instanceof User) {
            $department = $ts->getToken()->getUser()->getDepartment();
            $this->semester = $em->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
        }
        $this->em = $em;
    }

    public function setDepartment(Department $department)
    {
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

    public function getFemaleCount(): int
    {
        return $this->applicationRepository->numOfGender($this->semester, 1);
    }

    public function getPreviousParticipationCount(): int
    {
        return $this->applicationRepository->numOfPreviousParticipation($this->semester);
    }

    public function getCancelledInterviewsCount(): int
    {
        return count($this->applicationRepository->findCancelledApplicants($this->semester));
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
}
