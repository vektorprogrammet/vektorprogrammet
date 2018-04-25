<?php


namespace AppBundle\Service;


use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Semester;

class AdmissionSubscriberStatistics {
	/**
	 * @param AdmissionSubscriber[] $subscribers
	 * @param Semester $semester
	 *
	 * @return array
	 */
	public function generateGraphDataFromSubscribersInSemester($subscribers, Semester $semester) {
		$subData = $this->initializeSubscriberData($semester);
		return $this->populateSubscriberDataWithSubscribers($subData, $subscribers);
	}

	private function initializeSubscriberData(Semester $semester) {
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
	 * @param array $subData
	 * @param AdmissionSubscriber[] $subscribers
	 *
	 * @return array
	 */
	private function populateSubscriberDataWithSubscribers($subData, $subscribers) {
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
