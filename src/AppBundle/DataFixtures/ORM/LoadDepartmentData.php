<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Department;

class LoadDepartmentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $department1 = new Department();
        $department1->setName('Norges teknisk-naturvitenskapelige universitet');
        $department1->setShortName('NTNU');
        $department1->setEmail('NTNU@mail.com');
        $department1->addSchool($this->getReference('school-1'));
        $department1->addSchool($this->getReference('school-2'));
        $department1->addSchool($this->getReference('school-00'));
        $department1->addSchool($this->getReference('school-01'));
        $department1->addSchool($this->getReference('school-02'));
        $department1->addSchool($this->getReference('school-03'));
        $department1->addSchool($this->getReference('school-04'));
        $department1->addSchool($this->getReference('school-05'));
        $department1->addSchool($this->getReference('school-06'));
        $department1->addSchool($this->getReference('school-07'));
        $department1->addSchool($this->getReference('school-08'));
        $department1->addSchool($this->getReference('school-09'));
        $department1->setAddress('Høgskoleringen 5 7491 Trondheim');
        $department1->setCity('Trondheim');
        $manager->persist($department1);

        $department2 = new Department();
        $department2->setName('Høgskolen i Sør-Trønderlag');
        $department2->setShortName('HiST');
        $department2->setEmail('HiST@mail.com');
        $department2->addSchool($this->getReference('school-3'));
        $department2->setAddress('*hist adresse*');
        $department2->setCity('Trondheim');
        $manager->persist($department2);

        $department3 = new Department();
        $department3->setName('Norges miljø- og biovitenskapelige universitet');
        $department3->setShortName('NMBU');
        $department3->setEmail('NMBU@mail.com');
        $department3->addSchool($this->getReference('school-1'));
        $department3->setAddress('*NMBU adresse*');
        $department3->setCity('Ås');
        $manager->persist($department3);

        $department4 = new Department();
        $department4->setName('Universitetet i Oslo');
        $department4->setShortName('UiO');
        $department4->setEmail('UiO@mail.com');
        $department4->addSchool($this->getReference('school-2'));
        $department4->setAddress('*UiO adresse*');
        $department4->setCity('Oslo');
        $manager->persist($department4);

        $manager->flush();

        $this->addReference('dep-1', $department1);
        $this->addReference('dep-2', $department2);
        $this->addReference('dep-3', $department3);
        $this->addReference('dep-4', $department4);
    }

    public function getOrder()
    {
        return 2;
    }
}
