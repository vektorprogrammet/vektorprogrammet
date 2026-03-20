<?php

namespace App\Organization\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Organization\Infrastructure\Repository\TeamRepository;
use App\Organization\Infrastructure\Entity\Team;
use App\Identity\Domain\Roles;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepo,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    #[Route('/it', name: 'team_page_it', defaults: ['id' => 9], methods: ['GET'])]
    #[Route('/team/{id}', name: 'team_page', methods: ['GET'])]
    public function showAction(Team $team)
    {
        if (!$team->isActive() && !$this->isGranted(Roles::TEAM_MEMBER)) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->render('team/team_page.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/team/{departmentCity}/{teamName}', name: 'team_page_department_team', methods: ['GET'])]
    public function showByDepartmentAndTeamAction($departmentCity, $teamName)
    {
        $teams = $this->teamRepo->findByCityAndName($departmentCity, $teamName);
        if (count($teams) !== 1) {
            throw new NotFoundHttpException('Team not found');
        }

        return $this->showAction($teams[0]);
    }
}
