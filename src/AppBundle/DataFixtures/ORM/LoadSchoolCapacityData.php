<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SchoolCapacity;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSchoolCapacityData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $randomArr = array(true, false, false, false, false);
            shuffle($randomArr);
            $schoolCapacity = new SchoolCapacity();
            $schoolCapacity->setSchool($this->getReference('school-0'.$i));
            $schoolCapacity->setSemester($this->getReference('semester-current'));
            $schoolCapacity->setMonday($randomArr[0] || mt_rand(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setTuesday($randomArr[1] || mt_rand(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setWednesday($randomArr[2] || mt_rand(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setThursday($randomArr[3] || mt_rand(0, 100) < 30 ? 2 : 0);
            $schoolCapacity->setFriday($randomArr[4] || mt_rand(0, 100) < 30 ? 2 : 0);

            $manager->persist($schoolCapacity);
            $this->addReference('school-capacity-'.$i, $schoolCapacity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
