<?php

namespace AppBundle\Service;

use AppBundle\AssistantScheduling\Assistant;
use AppBundle\AssistantScheduling\School;
use AppBundle\Entity\Application;
use AppBundle\Entity\SchoolCapacity;
use AppBundle\Service\Contract\AssistantSchedulingDataServiceInterface;

/**
 * Service for transforming application and school capacity data for scheduling algorithm.
 */
class AssistantSchedulingDataService implements AssistantSchedulingDataServiceInterface
{
    /**
     * Transform applications to Assistant objects for scheduling algorithm.
     *
     * @param Application[] $applications
     * @return Assistant[]
     */
    public function transformApplicationsToAssistants(array $applications): array
    {
        $assistants = array();
        foreach ($applications as $application) {
            $doublePosition = $application->getDoublePosition();
            $preferredGroup = $this->mapPreferredGroup($application->getPreferredGroup(), $doublePosition);

            $availability = $this->buildAvailabilityArray($application);

            $assistant = new Assistant();
            $assistant->setName($application->getUser()->getFullName());
            $assistant->setEmail($application->getUser()->getEmail());
            $assistant->setDoublePosition($doublePosition);
            $assistant->setPreferredGroup($preferredGroup);
            $assistant->setAvailability($availability);
            $assistant->setApplication($application);
            $assistant->setScore($this->calculateAssistantScore($application));
            $assistant->setSuitability($this->getAssistantSuitability($application));
            $assistant->setPreviousParticipation($application->getPreviousParticipation());
            $assistants[] = $assistant;
        }

        return $assistants;
    }

    /**
     * Transform school capacities to School objects for scheduling algorithm.
     *
     * @param SchoolCapacity[] $schoolCapacities
     * @return School[]
     */
    public function transformSchoolCapacitiesToSchools(array $schoolCapacities): array
    {
        $schools = array();
        foreach ($schoolCapacities as $schoolCapacity) {
            $capacityDays = array(
                'Monday' => $schoolCapacity->getMonday(),
                'Tuesday' => $schoolCapacity->getTuesday(),
                'Wednesday' => $schoolCapacity->getWednesday(),
                'Thursday' => $schoolCapacity->getThursday(),
                'Friday' => $schoolCapacity->getFriday(),
            );

            $capacity = array(
                1 => $capacityDays,
                2 => $capacityDays,
            );

            $school = new School($capacity, $schoolCapacity->getSchool()->getName(), $schoolCapacity->getId());
            $schools[] = $school;
        }

        return $schools;
    }

    /**
     * Calculate assistant score based on application data.
     *
     * @param Application $application
     * @return int
     */
    public function calculateAssistantScore(Application $application): int
    {
        if ($application->getPreviousParticipation()) {
            return 20;
        }

        $interview = $application->getInterview();
        if ($interview !== null) {
            return $interview->getScore();
        }

        return 0;
    }

    /**
     * Get suitability string for assistant based on application data.
     *
     * @param Application $application
     * @return string
     */
    public function getAssistantSuitability(Application $application): string
    {
        if ($application->getPreviousParticipation()) {
            return 'Ja';
        }

        $interview = $application->getInterview();
        if ($interview !== null && $interview->getInterviewScore() !== null) {
            return $interview->getInterviewScore()->getSuitableAssistant();
        }

        return '';
    }

    /**
     * Map preferred group string to integer.
     *
     * @param string|null $preferredGroupString
     * @param bool $doublePosition
     * @return int|null
     */
    private function mapPreferredGroup($preferredGroupString, bool $doublePosition)
    {
        if ($doublePosition) {
            return null;
        }

        switch ($preferredGroupString) {
            case 'Bolk 1':
                return 1;
            case 'Bolk 2':
                return 2;
            default:
                return null;
        }
    }

    /**
     * Build availability array from application.
     *
     * @param Application $application
     * @return array
     */
    private function buildAvailabilityArray(Application $application): array
    {
        return array(
            'Monday' => $application->isMonday(),
            'Tuesday' => $application->isTuesday(),
            'Wednesday' => $application->isWednesday(),
            'Thursday' => $application->isThursday(),
            'Friday' => $application->isFriday(),
        );
    }
}

