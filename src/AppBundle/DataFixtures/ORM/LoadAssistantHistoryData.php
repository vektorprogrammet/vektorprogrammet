<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssistantHistory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAssistantHistoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $ah1 = new AssistantHistory();
        $ah1->setUser($this->getReference('user-1'));
        $ah1->setSchool($this->getReference('school-1'));
        $ah1->setSemester($this->getReference('semester-current'));
        $ah1->setWorkdays(4);
        $ah1->setBolk('Bolk 2');
        $ah1->setDay("Onsdag");
        $manager->persist($ah1);

        $ah2 = new AssistantHistory();
        $ah2->setUser($this->getReference('user-14'));
        $ah2->setSchool($this->getReference('school-2'));
        $ah2->setSemester($this->getReference('semester-current'));
        $ah2->setWorkdays(8);
        $ah2->setBolk('Bolk 1, Bolk 2');
        $ah2->setDay("Mandag");
        $manager->persist($ah2);

        $ah3 = new AssistantHistory();
        $ah3->setUser($this->getReference('user-14'));
        $ah3->setSchool($this->getReference('school-1'));
        $ah3->setSemester($this->getReference('semester-1'));
        $ah3->setWorkdays(4);
        $ah3->setBolk('Bolk 1');
        $ah3->setDay("Fredag");
        $manager->persist($ah3);
        $manager->flush();

        $this->addReference('ah-1', $ah1);

    }

    public function getOrder()
    {
        return 5;
    }
}