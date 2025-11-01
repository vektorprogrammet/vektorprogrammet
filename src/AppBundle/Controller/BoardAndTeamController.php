<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\ExecutiveBoard;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;
use Doctrine\ORM\EntityManagerInterface;

class BoardAndTeamController extends BaseController
{
    private $departmentRepository;
    private $userRepository;
    private $geoLocation;
    private $entityManager;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param GeoLocationInterface $geoLocation
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository,
        UserRepositoryInterface $userRepository,
        GeoLocationInterface $geoLocation,
        EntityManagerInterface $entityManager
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->geoLocation = $geoLocation;
        $this->entityManager = $entityManager;
    }
    public function showAction()
    {
        // Find all departments
        $departments = $this->departmentRepository->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $board = $this->entityManager->getRepository(ExecutiveBoard::class)->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->getCurrentSemester();
            $departmentStats[$department->getCity()] = array(
                'numTeamMembers' => sizeof($this->userRepository->findUsersInDepartmentWithTeamMembershipInSemester($department, $currentSemester)),
                'numAssistants' => sizeof($this->userRepository->findUsersWithAssistantHistoryInDepartmentAndSemester($department, $currentSemester)),
            );
        }

        return $this->render('team/board_and_team.html.twig', array(
            'departments' => $departments,
            'board' => $board,
            'numberOfTeams' => $numberOfTeams,
            'departmentStats' => $departmentStats,
        ));
    }
}
