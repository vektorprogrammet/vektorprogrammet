<?php

namespace App\DataFixtures\ORM;

use App\Entity\Department;
use App\Entity\School;
use App\Entity\SchoolCapacity;
use App\Shared\Entity\Semester;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadSchoolCapacityData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $randomArr = [true, false, false, false, false];
            shuffle($randomArr);
            $schoolCapacity = new SchoolCapacity();
            $schoolCapacity->setSchool($this->getReference('school-0'.$i, School::class));
            $schoolCapacity->setSemester($this->getReference('semester-current', Semester::class));
            $schoolCapacity->setDepartment($this->getReference('dep-1', Department::class));
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

    public function getOrder(): int
    {
        return 4;
    }
}
