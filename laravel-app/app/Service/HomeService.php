<?php

namespace App\Service;

use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Contract\AssistantHistoryRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Repository\Contract\UserRepositoryInterface;
use App\Service\Contract\GeoLocationInterface;
use App\Service\Contract\HomeServiceInterface;

/**
 * Service for aggregating home page data.
 * Extracts business logic from HomeController.
 */
class HomeService implements HomeServiceInterface
{
    private $userRepository;
    private $articleRepository;
    private $departmentRepository;
    private $assistantHistoryRepository;
    private $geoLocation;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param ArticleRepositoryInterface $articleRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param GeoLocationInterface $geoLocation
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        ArticleRepositoryInterface $articleRepository,
        DepartmentRepositoryInterface $departmentRepository,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        GeoLocationInterface $geoLocation
    ) {
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->geoLocation = $geoLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function getHomePageData(): array
    {
        $assistantsCount = count($this->userRepository->findAssistants());
        $teamMembersCount = count($this->userRepository->findTeamMembers());
        $articles = $this->articleRepository->findStickyAndLatestArticles();

        $departments = $this->departmentRepository->findAll();
        $departmentsWithActiveAdmission = $this->departmentRepository->findAllWithActiveAdmission();
        $departmentsWithActiveAdmission = $this->geoLocation->sortDepartmentsByDistanceFromClient(
            $departmentsWithActiveAdmission
        );
        $closestDepartment = $this->geoLocation->findNearestDepartment($departments);
        $ipWasLocated = $this->geoLocation->findCoordinatesOfCurrentRequest();

        $femaleAssistantCount = $this->assistantHistoryRepository->numFemale();
        $maleAssistantCount = $this->assistantHistoryRepository->numMale();

        return [
            'assistantCount' => $assistantsCount + 600, // + Estimated number of assistants not registered in website
            'teamMemberCount' => $teamMembersCount + 160, // + Estimated number of team members not registered in website
            'femaleAssistantCount' => $femaleAssistantCount,
            'maleAssistantCount' => $maleAssistantCount,
            'ipWasLocated' => $ipWasLocated,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'closestDepartment' => $closestDepartment,
            'news' => $articles,
        ];
    }
}

