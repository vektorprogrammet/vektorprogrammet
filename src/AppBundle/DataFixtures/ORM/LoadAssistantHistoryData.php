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
        $ah1->setSemester($this->getReference('semester-current'));
        $ah1->setExtraWorkDays(4);
        $ah1->setBolk('Bolk 2');
        $manager->persist($ah1);

        $ah2 = new AssistantHistory();
        $ah2->setUser($this->getReference('user-assistant'));
        $ah2->setSemester($this->getReference('semester-current'));
        $ah2->setExtraWorkDays(8);
        $ah2->setBolk('Bolk 1, Bolk 2');
        $manager->persist($ah2);

        $ah3 = new AssistantHistory();
        $ah3->setUser($this->getReference('user-2'));
        $ah3->setSemester($this->getReference('semester-current'));
        $ah3->setExtraWorkDays(4);
        $ah3->setBolk('Bolk 1');
        $manager->persist($ah3);

        $ah4 = new AssistantHistory();
        $ah4->setUser($this->getReference('user-3'));
        $ah4->setSemester($this->getReference('semester-current'));
        $ah4->setExtraWorkDays(4);
        $ah4->setBolk('Bolk 1');
        $manager->persist($ah4);

        $ah5 = new AssistantHistory();
        $ah5->setUser($this->getReference('user-4'));
        $ah5->setSemester($this->getReference('semester-1'));
        $ah5->setExtraWorkDays(4);
        $ah5->setBolk('Bolk 1');
        $manager->persist($ah5);

        $ah6 = new AssistantHistory();
        $ah6->setUser($this->getReference('user-4'));
        $ah6->setSemester($this->getReference('semester-2'));
        $ah6->setExtraWorkDays(4);
        $ah6->setBolk('Bolk 1');
        $manager->persist($ah6);

        $manager->flush();

        $this->addReference('ah-1', $ah1);
        $this->addReference('ah-2', $ah2);
        $this->addReference('ah-3', $ah3);
        $this->addReference('ah-4', $ah4);
    }

    public function getOrder()
    {
        return 5;
    }
}
