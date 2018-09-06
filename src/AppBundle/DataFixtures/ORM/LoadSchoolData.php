<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\School;

class LoadSchoolData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $school1 = new School();
        $school1->setName('Gimse');
        $school1->setContactPerson('Per Olsen');
        $school1->setEmail('Per@mail.com');
        $school1->setPhone('99887722');
        //        $school1->addDepartment($this->getReference('dep-1'));

        $school2 = new School();
        $school2->setName('Selsbakk');
        $school2->setContactPerson('Vibeke Hansen');
        $school2->setEmail('Vibeke@mail.com');
        $school2->setPhone('22386722');
        //        $school2->addDepartment($this->getReference('dep-1'));

        $school3 = new School();
        $school3->setName('Blussuvoll');
        $school3->setContactPerson('Kari Johansen');
        $school3->setEmail('kari@mail.com');
        $school3->setPhone('22386722');
        $school3->setInternational(true);
        //        $school3->addDepartment($this->getReference('dep-1'));

        $school4 = new School();
        $school4->setName('Katta');
        $school4->setContactPerson('Jon Andreas Støvneng');
        $school4->setEmail('jas@mail.com');
        $school4->setPhone('13424567');
        $school4->setActive(false);

        $manager->persist($school1);
        $manager->persist($school2);
        $manager->persist($school3);
        $manager->persist($school4);

        for ($i = 0; $i < 10; ++$i) {
            $school = new School();
            $school->setName('Skole '.$i);
            $school->setContactPerson('Kontaktperson '.$i);
            $school->setEmail('skole@mail.com');
            $school->setPhone('12345678');
            $school->setInternational(false);
            $manager->persist($school);
            $this->addReference('school-0'.$i, $school);
        }

        $manager->flush();

        $this->addReference('school-1', $school1);
        $this->addReference('school-2', $school2);
        $this->addReference('school-3', $school3);
        $this->addReference('school-4', $school4);
    }

    public function getOrder()
    {
        return 1;
    }
}
