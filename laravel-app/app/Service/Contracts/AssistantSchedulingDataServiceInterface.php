<?php

namespace App\Contract;

use App\AssistantScheduling\Assistant;
use App\AssistantScheduling\School;
use App\Models\Application;
use App\Models\SchoolCapacity;

/**
 * Interface for AssistantSchedulingDataService.
 * Defines contract for transforming application and school capacity data for scheduling algorithm.
 */
interface AssistantSchedulingDataServiceInterface
{
    /**
     * Transform applications to Assistant objects for scheduling algorithm.
     *
     * @param Application[] $applications
     * @return Assistant[]
     */
    public function transformApplicationsToAssistants(array $applications): array;

    /**
     * Transform school capacities to School objects for scheduling algorithm.
     *
     * @param SchoolCapacity[] $schoolCapacities
     * @return School[]
     */
    public function transformSchoolCapacitiesToSchools(array $schoolCapacities): array;

    /**
     * Calculate assistant score based on application data.
     *
     * @param Application $application
     * @return int
     */
    public function calculateAssistantScore(Application $application): int;

    /**
     * Get suitability string for assistant based on application data.
     *
     * @param Application $application
     * @return string
     */
    public function getAssistantSuitability(Application $application): string;
}

