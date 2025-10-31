<?php

namespace AppBundle\Service\Contract;

/**
 * Interface for Home service operations.
 * This interface defines the contract for home page data aggregation.
 */
interface HomeServiceInterface
{
    /**
     * Get all home page data including statistics, articles, and departments.
     *
     * @return array Home page data with keys:
     *   - assistantCount: int (with estimated additions)
     *   - teamMemberCount: int (with estimated additions)
     *   - femaleAssistantCount: int
     *   - maleAssistantCount: int
     *   - ipWasLocated: array|null
     *   - departmentsWithActiveAdmission: Department[]
     *   - closestDepartment: Department|null
     *   - news: Article[]
     */
    public function getHomePageData(): array;
}

