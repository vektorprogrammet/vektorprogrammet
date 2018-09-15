<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Semester;
use AppBundle\Entity\WorkDay;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWorkDayData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $currentSemester = $this->getReference('semester-current');
        $firstWeek = $currentSemester->getSemesterStartDate()->modify("+20 days")->format('W');

        for($i = 5; $i < 9; $i++) {
            $wd = new WorkDay($this->getReference('ah-1'));
            $wd->setDate($this->convertWeekNumberToDateTime($firstWeek + $i, 1, $currentSemester));
            $wd->setSchool($this->getReference('school-1'));
            $wd->setTerm(2);
            $manager->persist($wd);
        }

        for($i = 0; $i < 9; $i++) {
            if ($i === 4) {
                continue;
            }
            $wd = new WorkDay($this->getReference('ah-2'));
            $wd->setDate($this->convertWeekNumberToDateTime($firstWeek + $i, 1, $currentSemester));
            $wd->setSchool($this->getReference('school-1'));
            $wd->setTerm(intdiv($i - 1, 4) + 1);
            $manager->persist($wd);
        }

        for($j = 3; $j <= 4; $j++) {
            for ($i = 0; $i < 4; $i++) {
                $wd = new WorkDay($this->getReference("ah-$j"));
                $wd->setDate($this->convertWeekNumberToDateTime($firstWeek + $i, 1, $currentSemester));
                $wd->setSchool($this->getReference('school-1'));
                $wd->setTerm(1);
                $manager->persist($wd);
            }
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 6;
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
}
