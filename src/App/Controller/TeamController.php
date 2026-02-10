<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Repository\TeamRepository;
use App\Entity\Team;
use App\Role\Roles;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeamController extends BaseController
{
    public function __construct(
        private TeamRepository $teamRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    public function showAction(Team $team)
    {
        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', array(
            'team'  => $team,
        ));
    }

    public function showByDepartmentAndTeamAction($departmentCity, $teamName)
    {
        $teams = $this->teamRepo->findByCityAndName($departmentCity, $teamName);
        if (count($teams) !== 1) {
            throw new NotFoundHttpException('Team not found');
        }
        return $this->showAction($teams[0]);
    }
}
