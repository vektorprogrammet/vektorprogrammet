<?php

namespace App\Contract;

use App\Models\AdmissionPeriod;
use App\Models\Application;

/**
 * Interface for InterviewCounter service.
 * Defines contract for interview counting operations.
 */
interface InterviewCounterInterface
{
    /**
     * Count applications by suitability.
     *
     * @param Application[] $applications
     * @param string $suitable
     * @return int
     */
    public function count(array $applications, string $suitable): int;

    /**
     * Create interview distributions.
     *
     * @param Application[] $applications
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function createInterviewDistributions(array $applications, AdmissionPeriod $admissionPeriod): array;
}
