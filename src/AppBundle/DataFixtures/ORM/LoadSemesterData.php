<?php

namespace AppBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Semester;

class LoadSemesterData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $now = new DateTime();
        $jul = 7;
        $isSpring = $now->format('n') <= $jul;

        $currentSemester = new Semester();
        $currentSemester->setSemesterTime($isSpring ? 'Vår' : 'Høst');
        $currentSemester->setYear($now->format('Y'));
        $manager->persist($currentSemester);

        $previousSemester = new Semester();
        $previousSemester->setSemesterTime($isSpring ? 'Høst' : 'Vår');
        $previousSemester->setYear($isSpring ? $now->format('Y') - 1 : $now->format('Y'));
        $manager->persist($previousSemester);

        $semester1 = new Semester();
        $semester1->setSemesterTime('Vår');
        $semester1->setYear(2013);
        $manager->persist($semester1);

        $semester2 = new Semester();
        $semester2->setSemesterTime('Vår');
        $semester2->setYear(2015);
        $manager->persist($semester2);

        $semester3 = new Semester();
        $semester3->setSemesterTime('Høst');
        $semester3->setYear(2015);
        $manager->persist($semester3);

        $manager->flush();

        $this->addReference('semester-current', $currentSemester);
        $this->addReference('semester-previous', $previousSemester);
        $this->addReference('semester-1', $semester1);
        $this->addReference('semester-2', $semester2);
        $this->addReference('semester-3', $semester3);
    }

    public function getOrder()
    {
        return 3;
    }
}
