<?php

namespace AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Department;
use AppBundle\Entity\Repository\ApplicationRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApplicationData
{
    /**
     * @var Department
     */
    private $department;
    /**
     * @var AdmissionPeriod
     */
    private $admissionPeriod;
    /**
     * @var ApplicationRepository
     */
    private $applicationRepository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ApplicationData constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $ts
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts)
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
        return count($this->em->getRepository('AppBundle:Application')->findInterviewedApplicants($this->admissionPeriod));
    }

    public function getAssignedInterviewsCount(): int
    {
        return count($this->em->getRepository('AppBundle:Application')->findAssignedApplicants($this->admissionPeriod));
    }

    public function getTotalAssistantsCount(): int
    {
        return count($this->em->getRepository('AppBundle:AssistantHistory')->findByDepartmentAndSemester($this->department, $this->admissionPeriod->getSemester()));
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->em->getRepository('AppBundle:AssistantHistory')->findByDepartmentAndSemester($this->department, $this->admissionPeriod->getSemester());

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
        $applicants = $this->applicationRepository->findBy(array('admissionPeriod' => $this->admissionPeriod));
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
        $applicants = $this->applicationRepository->findBy(array('admissionPeriod' => $this->admissionPeriod));
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
        $heardAbout = array();
        $applicants = $this->applicationRepository->findBy(array('admissionPeriod' => $this->admissionPeriod));

        foreach ($applicants as $applicant) {
            $allHeardAboutFrom = $applicant->getHeardAboutFrom();

            if ($allHeardAboutFrom === null) {
                $allHeardAboutFrom = array(0=>"Ingen");
            }

            for ($i = 0; $i < count($allHeardAboutFrom); $i++) {
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
