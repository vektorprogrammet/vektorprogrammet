<?php


namespace AppBundle\Service;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;

class AdmissionStatistics
{
    /**
     * @param AdmissionSubscriber[] $subscribers
     * @param Semester $semester
     *
     * @return array
     */
    public function generateGraphDataFromSubscribersInSemester($subscribers, Semester $semester)
    {
        $subData = $this->initializeDataArray($semester);
        return $this->populateSubscriberDataWithSubscribers($subData, $subscribers);
    }

    /**
     * @param Application[] $applications
     * @param Semester $semester
     *
     * @return array
     */
    public function generateGraphDataFromApplicationsInSemester($applications, Semester $semester)
    {
        $appData = $this->initializeDataArray($semester);
        return $this->populateApplicationDataWithApplications($appData, $applications);
    }

    private function initializeDataArray(Semester $semester)
    {
        $subData = [];

        $now = new \DateTime();
        $days = $semester->getSemesterStartDate()->diff($now)->days;
        if ($now > $semester->getSemesterEndDate()) {
            $days = $semester->getSemesterStartDate()->diff($semester->getSemesterEndDate())->days;
        }
        $start = $semester->getSemesterStartDate()->format('Y-m-d');
        for ($i = 0; $i < $days; $i++) {
            $date = (new \DateTime($start))->modify("+$i days")->format('Y-m-d');
            $subData[$date] = 0;
        }

        return $subData;
    }

    /**
     * @param array $appData
     * @param Application[] $applications
     *
     * @return array
     */
    private function populateApplicationDataWithApplications($appData, $applications)
    {
        foreach ($applications as $application) {
            $date = $application->getCreated()->format('Y-m-d');
            if (!isset($appData[$date])) {
                $appData[$date] = 0;
            }
            $appData[$date]++;
        }
        ksort($appData);

        return $appData;
    }

    /**
     * @param array $subData
     * @param AdmissionSubscriber[] $subscribers
     *
     * @return array
     */
    private function populateSubscriberDataWithSubscribers($subData, $subscribers)
    {
        foreach ($subscribers as $subscriber) {
            $date = $subscriber->getTimestamp()->format('Y-m-d');
            if (!isset($subData[$date])) {
                $subData[$date] = 0;
            }
            $subData[$date]++;
        }
        ksort($subData);

        return $subData;
    }
}
