<?php

namespace AppBundle\Service;


use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Repository\AssistantHistoryRepository;
use AppBundle\Entity\Semester;
use AppBundle\Entity\WorkDay;
use AppBundle\Model\AssistantDelegationInfo;
use Doctrine\ORM\EntityManager;
use PhpCollection\Set;

class WorkDayManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Creates, persists and flushes workdays.
     *
     * **Important note**: Also persists the assistantPosition!
     *
     * @param AssistantDelegationInfo $info
     * @param AssistantHistory $assistantPosition
     *
     * @return array
     */
    public function createAndPersistWorkDays(AssistantDelegationInfo $info, AssistantHistory $assistantPosition)
    {
        $semester = $assistantPosition->getSemester();
        /** @var WorkDay[] $workDays */
        $workDays = array();
        for($i = 0; $i < $info->getNumDays(); $i++) {
            $workDays[] = new WorkDay($assistantPosition);
        }

        $startDate = $this->convertWeekNumberToDateTime($info->getStartingWeek(), $info->getWeekDay(), $semester);
        $weekDiff = $this->dateDiffInWeeks($startDate, $semester->getSemesterEndDate());
        $dateFrequencyTable = array();
        for($week = $info->getStartingWeek(); $week < $info->getStartingWeek() + $weekDiff; $week++) {
            $date = $this->convertWeekNumberToDateTime($week, $info->getWeekDay(), $semester);
            $dateFrequencyTable[$week] = $this->em->getRepository('AppBundle:WorkDay')
                ->createQueryBuilder('workDay')
                ->select('count(workDay)')
                ->innerJoin('workDay.assistantPosition', 'assistantPosition')
                ->where('workDay.date = :date')
                ->andWhere('assistantPosition.semester = :semester')
                ->andWhere('assistantPosition != :assistantPosition')
                ->setParameters(array(
                    'date'              => $date,
                    'semester'          => $semester,
                    'assistantPosition' => $assistantPosition,
                ))
                ->getQuery()
                ->getSingleScalarResult();
        }


        dump($dateFrequencyTable);

        /*
        $this->persistWorkDays($workDays);
        $this->em->persist($assistantPosition);
        $this->em->flush();
        */
        return $workDays;
    }

    /**
     * Finds and returns the most common element in $array
     * @param array $array
     *
     * @return array
     */
    private function findMostCommonElement(array $array)
    {
        $values = array_count_values($array);
        arsort($values);
        return key($values);
    }

    private function setInitialWorkDayData(array $workDays, AssistantDelegationInfo $info, Semester $semester)
    {
        /** @var WorkDay $workDay */
        foreach ($workDays as  $i => $workDay) {
            $date = $this->convertWeekNumberToDateTime(
                $info->getStartingWeek() + $i,
                $info->getWeekDay(),
                $semester
            );

            $workDay->setDate($date);
            $workDay->setSchool($info->getSchool());
        }
    }

    /**
     * @param WorkDay[] $workDays
     */
    private function persistWorkDays(array $workDays)
    {
        foreach($workDays as $workDay) {
            $this->em->persist($workDay);
        }
    }

    /**
     * @param int $weekNumber
     * @param int $weekDay – Monday is 0 sunday is 6
     * @param Semester $semester
     *
     * @return \DateTime
     */
    private function convertWeekNumberToDateTime(int $weekNumber, int $weekDay, Semester $semester)
    {
        $date = new \DateTime();
        $semesterStartYear = $semester->getSemesterStartDate()->format('Y');
        $date->setISODate($semesterStartYear, $weekNumber, $weekDay);
        $date->setTime(8,0); // Set time to something fixed instead of current time
        return $date;
    }

    function dateDiffInWeeks($date1, $date2)
    {
        if($date1 > $date2) return $this->dateDiffInWeeks($date2, $date1);
        return floor($date1->diff($date2)->days/7);
    }
}
