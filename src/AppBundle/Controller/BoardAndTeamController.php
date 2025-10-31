<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\ExecutiveBoardRepositoryInterface;
use AppBundle\Repository\Contract\UserRepositoryInterface;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\GeoLocationInterface;

class BoardAndTeamController extends BaseController
{
    private $departmentRepository;
    private $geoLocation;
    private $userRepository;
    private $semesterRepository;
    private $executiveBoardRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param GeoLocationInterface $geoLocation
     * @param UserRepositoryInterface $userRepository
     * @param SemesterRepositoryInterface $semesterRepository
     * @param ExecutiveBoardRepositoryInterface $executiveBoardRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository,
        GeoLocationInterface $geoLocation,
        UserRepositoryInterface $userRepository,
        SemesterRepositoryInterface $semesterRepository,
        ExecutiveBoardRepositoryInterface $executiveBoardRepository
    ) {
        $this->departmentRepository = $departmentRepository;
        $this->geoLocation = $geoLocation;
        $this->userRepository = $userRepository;
        $this->semesterRepository = $semesterRepository;
        $this->executiveBoardRepository = $executiveBoardRepository;
    }

    public function showAction()
    {
        // Find all departments
        $departments = $this->departmentRepository->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        
        $board = $this->executiveBoardRepository->findBoard();

        $numberOfTeams = 0;
        foreach ($departments as $department) {
            $numberOfTeams += $department->getTeams()->count();
        }

        $departmentStats = array();
        /** @var \AppBundle\Entity\Department $department */
        foreach ($departments as $department) {
            $currentSemester = $this->semesterRepository->findOrCreateCurrentSemester();
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
