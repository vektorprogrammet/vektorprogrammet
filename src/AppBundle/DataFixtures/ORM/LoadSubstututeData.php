<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Substitute;

class LoadSubstituteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sub1 = new Substitute();
        $sub1->setFirstName('Marius');
        $sub1->setLastName('Svendsen');
        $sub1->setEmail('marius@svendsven.no');
        $sub1->setPhone('95128535');
        $sub1->setFieldOfStudy($this->getReference('fos-1'));
        $sub1->setYearOfStudy(1);
        $sub1->setSemester($this->getReference('semester-current'));
        $sub1->setMonday("Bra");
        $sub1->setTuesday("Ikke");
        $sub1->setWednesday("Ikke");
        $sub1->setThursday("Ok");
        $sub1->setFriday("Bra");

        $manager->persist($sub1);

        $sub2 = new Substitute();
        $sub2->setFirstName('Amalie');
        $sub2->setLastName('Nilsen');
        $sub2->setEmail('amalie@nilsen.no');
        $sub2->setPhone('45872561');
        $sub2->setYearOfStudy(2);
        $sub2->setFieldOfStudy($this->getReference('fos-1'));
        $sub2->setSemester($this->getReference('semester-current'));
        $sub2->setMonday("Ok");
        $sub2->setTuesday("Ikke");
        $sub2->setWednesday("Ok");
        $sub2->setThursday("Bra");
        $sub2->setFriday("Bra");

        $manager->persist($sub2);

        $sub3 = new Substitute();
        $sub3->setFirstName('Sebastian');
        $sub3->setLastName('Kleiveland');
        $sub3->setEmail('sebastian@kleiveland.no');
        $sub3->setPhone('97586258');
        $sub3->setYearOfStudy(3);
        $sub3->setFieldOfStudy($this->getReference('fos-1'));
        $sub3->setSemester($this->getReference('semester-current'));
        $sub3->setMonday("Bra");
        $sub3->setTuesday("Bra");
        $sub3->setWednesday("Ok");
        $sub3->setThursday("Bra");
        $sub3->setFriday("Ikke");

        $manager->persist($sub3);

        $sub4 = new Substitute();
        $sub4->setFirstName('Elisabeth');
        $sub4->setLastName('Kleven');
        $sub4->setEmail('elisabeth@kleven.no');
        $sub4->setPhone('95147835');
        $sub4->setYearOfStudy(1);
        $sub4->setFieldOfStudy($this->getReference('fos-1'));
        $sub4->setSemester($this->getReference('semester-current'));
        $sub4->setMonday("Ikke");
        $sub4->setTuesday("Bra");
        $sub4->setWednesday("Ok");
        $sub4->setThursday("Ikke");
        $sub4->setFriday("Ok");

        $manager->persist($sub4);

        $sub5 = new Substitute();
        $sub5->setFirstName('Sonja');
        $sub5->setLastName('Vågen');
        $sub5->setEmail('sonja@vaagen.no');
        $sub5->setPhone('95142536');
        $sub5->setYearOfStudy(4);
        $sub5->setFieldOfStudy($this->getReference('fos-1'));
        $sub5->setSemester($this->getReference('semester-current'));
        $sub5->setMonday("Bra");
        $sub5->setTuesday("Bra");
        $sub5->setWednesday("Ikke");
        $sub5->setThursday("Bra");
        $sub5->setFriday("Ok");

        $manager->persist($sub5);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}