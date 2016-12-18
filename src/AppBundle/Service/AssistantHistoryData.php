<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AssistantHistoryData
{
    private $assistantHistoryRepository;
    private $semester;

    public function __construct(EntityManager $em, TokenStorage $ts)
    {
        $this->assistantHistoryRepository = $em->getRepository('AppBundle:AssistantHistory');
        $department = $ts->getToken()->getUser()->getDepartment();
        $this->semester = $em->getRepository('AppBundle:Semester')->findLatestSemesterByDepartmentId($department->getId());
    }

    public function setSemester(Semester $semester)
    {
        $this->semester = $semester;
    }

    public function getAssistantHistoryCount(): int
    {
        return count($this->assistantHistoryRepository->findBySemester($this->semester));
    }

    public function getCount(): int
    {
        return $this->getAssistantHistoryCount();
    }

    public function getMaleCount(): int
    {
        return $this->assistantHistoryRepository->numMale($this->semester);
    }

    public function getFemaleCount(): int
    {
        return $this->assistantHistoryRepository->numFemale($this->semester);
    }

    public function getPositionsCount(): int
    {
        $assistantHistories = $this->assistantHistoryRepository->findBySemester($this->semester);
        $positionsCount = count($assistantHistories);
        foreach ($assistantHistories as $assistant) {
            if ($assistant->getBolk() === 'Bolk 1, Bolk 2') {
                ++$positionsCount;
            }
        }

        return $positionsCount;
    }
}
