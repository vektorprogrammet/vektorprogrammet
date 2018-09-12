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

        /*
         * Predict which days should follow the given startDate by looking at
         * other workDays in the same semester
         */
        $maxNumReferenceAssistantPositions = 5;
        $firstWorkDate = $this->convertWeekNumberToDateTime($info->getStartingWeek(), $info->getWeekDay(), $semester);
        $workDayRepository = $this->em->getRepository('AppBundle:WorkDay');

        // Create a cool queryBuilder
        $qb = $workDayRepository->createQueryBuilder('workDay')
            ->select('workDay.assistantPosition')
            ->join('workDay.assistantPosition', 'assistantPosition')
            ->where('assistantPosition.semester = :semester')
            ->andWhere('workDay.date = :firstWorkDate')
            ->setMaxResults($maxNumReferenceAssistantPositions)
            ->setParameter('semester', $semester)
            ->setParameter('firstWorkDate', $firstWorkDate);

        // We create a set of assistant positions for reference
        $referenceAssistantPositions = new Set();
        $referenceAssistantPositions->addAll($qb->getQuery()->getResult());

        // We then (possibly) add some with as many or more workdays as our assistant position
        // This is done to ensure ensure we do something stupid
        $referenceAssistantPositions->addAll($qb
            ->andWhere('count(assistantPosition.workDays) >= :numDays')
            ->setParameter('numDays', $info->getNumDays())
            ->getQuery()
            ->getResult());


        /**
         * Array containing {numDays} arrays of workDay dates. Array 1
         * corresponds to the first day, array 2 corresponds to the second and
         * so on. These will be traversed from day 1 all the way to day
         * {numDays} and the most common date will be picked for our
         * assistantPosition's workDay.
         */
        $dateArrays = array_fill(0, $info->getNumDays(), array());
        foreach($referenceAssistantPositions as $assistantPosition) {
            /** @var WorkDay[] $referenceAssistantWorkDays */
            $referenceAssistantWorkDays = $workDayRepository->findChronologicallyByAssistantPosition($assistantPosition);
            for($day = 0; $day < $info->getNumDays(); $day++) {
                $dateArrays[$day][] = $referenceAssistantWorkDays[$day]->getDate();
            }
        }

        for($day = 0; $day < $info->getNumDays(); $day++) {
            foreach($dateArrays[$day] as $referenceDates) {
                /** @var \DateTime $mostCommonDate */
                $mostCommonDate = $this->findMostCommonElement($referenceDates);
                $workDays[$day]->setDate($mostCommonDate);
            }
        }

        $this->persistWorkDays($workDays);
        $this->em->persist($assistantPosition);
        $this->em->flush();
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
     * @param int $startingWeek
     * @param int $weekDay – Monday is 0 sunday is 6
     * @param Semester $semester
     *
     * @return \DateTime
     */
    private function convertWeekNumberToDateTime(int $startingWeek, int $weekDay, Semester $semester)
    {
        $date = new \DateTime();
        $semesterStartYear = $semester->getSemesterStartDate()->format('Y');
        $date->setISODate($semesterStartYear, $startingWeek, $weekDay);
        $date->setTime(8,0); // Set time to something fixed instead of current time
        return $date;
    }
}
