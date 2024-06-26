<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssistantHistoryData
{
    private $assistantHistoryRepository;
    private $semester;
    private $department;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $ts, GeoLocation $geoLocation)
    {
        $this->assistantHistoryRepository = $em->getRepository(AssistantHistory::class);
        $user = $ts->getToken()->getUser();
        $departments = $em->getRepository(Department::class)->findAll();
        if ($user == "anon.") {
            $this->department = $geoLocation->findNearestDepartment($departments);
        } else {
            $this->department = $ts->getToken()->getUser()->getDepartment();
        }
        $this->semester = $em->getRepository(Semester::class)->findOrCreateCurrentSemester();
    }

    /**
     * @param Semester $semester
     *
     * @return $this
     */
    public function setSemester(Semester $semester)
    {
        $this->semester = $semester;
        return $this;
    }

    /**
     * @param Department $department
     *
     * @return AssistantHistoryData
     */
    public function setDepartment($department)
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
            if ($assistant->getBolk() === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }
}
