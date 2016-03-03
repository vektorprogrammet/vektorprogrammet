<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Semester;

class LoadSemesterData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $semester1 = new Semester();
//        $semester1->setName('NTNU Vår 2015');
        $semester1->setSemesterTime('Vår');
        $semester1->setYear(2015);
        $semester1->setDepartment($this->getReference('dep-1'));
        $semester1->setAdmissionStartDate(new \DateTime('2015-01-01'));
        $semester1->setAdmissionEndDate(new \DateTime('2015-05-30'));
        $semester1->setSemesterStartDate(new \DateTime('2015-01-01'));
        $semester1->setSemesterEndDate(new \DateTime('2015-06-30'));
        $manager->persist($semester1);

        $semester2 = new Semester();
//        $semester2->setName('HiST Vår 2015');
        $semester2->setSemesterTime('Vår');
        $semester2->setYear(2015);
        $semester2->setDepartment($this->getReference('dep-2'));
        $semester2->setAdmissionStartDate(new \DateTime('2015-01-01'));
        $semester2->setAdmissionEndDate(new \DateTime('2015-05-30'));
        $semester2->setSemesterStartDate(new \DateTime('2015-01-01'));
        $semester2->setSemesterEndDate(new \DateTime('2015-06-30'));
        $manager->persist($semester2);

        $semester3 = new Semester();
//        $semester3->setName('NMBU Vår 2015');
        $semester3->setSemesterTime('Vår');
        $semester3->setYear(2015);
        $semester3->setDepartment($this->getReference('dep-3'));
        $semester3->setAdmissionStartDate(new \DateTime('2015-01-01'));
        $semester3->setAdmissionEndDate(new \DateTime('2015-05-30'));
        $semester3->setSemesterStartDate(new \DateTime('2015-01-01'));
        $semester3->setSemesterEndDate(new \DateTime('2015-06-30'));
        $manager->persist($semester3);

        $semester4 = new Semester();
//        $semester4->setName('UiO Vår 2015');
        $semester4->setSemesterTime('Vår');
        $semester4->setYear(2015);
        $semester4->setDepartment($this->getReference('dep-4'));
        $semester4->setAdmissionStartDate(new \DateTime('2015-01-01'));
        $semester4->setAdmissionEndDate(new \DateTime('2015-05-30'));
        $semester4->setSemesterStartDate(new \DateTime('2015-01-01'));
        $semester4->setSemesterEndDate(new \DateTime('2015-06-30'));
        $manager->persist($semester4);

        $manager->flush();

        $this->addReference('semester-1', $semester1);
        $this->addReference('semester-2', $semester2);
        $this->addReference('semester-3', $semester3);
        $this->addReference('semester-4', $semester4);
    }

    public function getOrder()
    {
        return 3;
    }
}