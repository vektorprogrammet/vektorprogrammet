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
//        $ah1 = new AssistantHistory();
//        $ah1->setUser($this->getReference('user-1'));
//        $ah1->setSchool($this->getReference('school-1'));
//        $manager->persist($ah1);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}