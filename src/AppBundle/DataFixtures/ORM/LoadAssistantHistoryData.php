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
        $ah1->setSemester($this->getReference('semester-1'));
        $ah1->setWorkdays(4);
        $ah1->setBolk('Bolk 2');
        $ah1->setDay("Onsdag");
        $manager->persist($ah1);
        $manager->flush();

        $this->addReference('ah-1', $ah1);

    }

    public function getOrder()
    {
        return 5;
    }
}