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

        $school2 = new School();
        $school2->setName('Selsbakk');
		$school2->setContactPerson('Vibeke Hansen');
		$school2->setEmail('Vibeke@mail.com');
		$school2->setPhone('22386722');

        $school3 = new School();
        $school3->setName('Blussuvoll');
        $school3->setContactPerson('Kari Johansen');
        $school3->setEmail('kari@mail.com');
        $school3->setPhone('22386722');
        $school3->setInternational(true);
		
        $manager->persist($school1);
        $manager->persist($school2);
        $manager->persist($school3);

        $manager->flush();
		
		$this->addReference('school-1', $school1);
        $this->addReference('school-2', $school2);
        $this->addReference('school-3', $school3);
    }

    public function getOrder()
    {
        return 1;
    }
}