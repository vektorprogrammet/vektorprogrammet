<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\ArticleRepository;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\UserRepository;
use App\Support\Infrastructure\GeoLocation;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends BaseController
{
    private readonly DepartmentRepository $departmentRepo;

    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly ArticleRepository $articleRepo,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly GeoLocation $geoLocation,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
        $this->departmentRepo = $departmentRepo;
    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function showAction()
    {
        $assistantsCount = count($this->userRepo->findAssistants());
        $teamMembersCount = count($this->userRepo->findTeamMembers());
        $articles = $this->articleRepo->findStickyAndLatestArticles();

        $departments = $this->departmentRepo->findAll();
        $departmentsWithActiveAdmission = $this->departmentRepo->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $this->geoLocation->sortDepartmentsByDistanceFromClient($departmentsWithActiveAdmission);
        $closestDepartment = $this->geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $this->geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->assistantHistoryRepo->numFemale();
        $maleAssistantCount = $this->assistantHistoryRepo->numMale();

        return $this->render('home/index.html.twig', [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
            'news' => $articles,
        ]);
    }

    #[Route('/', name: 'home_post', methods: ['POST'])]
    public function postAction()
    {
        return $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ?autoplay=1', 301);
    }
}
