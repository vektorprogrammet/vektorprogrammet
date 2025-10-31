<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;

/**
 * Interface for AdmissionStatistics service.
 * Defines contract for admission statistics operations.
 */
interface AdmissionStatisticsInterface
{
    /**
     * Generate graph data from subscribers in semester.
     *
     * @param AdmissionSubscriber[] $subscribers
     * @param Semester $semester
     * @return array
     */
    public function generateGraphDataFromSubscribersInSemester(array $subscribers, Semester $semester): array;

    /**
     * Generate graph data from applications in admission period.
     *
     * @param Application[] $applications
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function generateGraphDataFromApplicationsInAdmissionPeriod(array $applications, AdmissionPeriod $admissionPeriod): array;

    /**
     * Generate cumulative graph data from applications in admission period.
     *
     * @param Application[] $applications
     * @param AdmissionPeriod $admissionPeriod
     * @return array
     */
    public function generateCumulativeGraphDataFromApplicationsInAdmissionPeriod(array $applications, AdmissionPeriod $admissionPeriod): array;
}
