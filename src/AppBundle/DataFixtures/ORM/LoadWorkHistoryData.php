<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\WorkHistory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadWorkHistoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $wh = new WorkHistory();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-1'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $manager->persist($wh);

        $wh2 = new WorkHistory();
        $wh2->setTeam($this->getReference('team-2'));
        $wh2->setUser($this->getReference('user-2'));
        $wh2->setStartSemester($this->getReference('semester-1'));
        $wh2->setPosition($this->getReference('position-2'));
        $manager->persist($wh2);

        $wh3 = new WorkHistory();
        $wh3->setTeam($this->getReference('team-1'));
        $wh3->setUser($this->getReference('user-4'));
        $wh3->setStartSemester($this->getReference('semester-1'));
        $wh3->setEndSemester($this->getReference('semester-1'));
        $wh3->setPosition($this->getReference('position-2'));
        $manager->persist($wh3);

        $manager->flush();

        $this->addReference('wh-1', $wh);
        $this->addReference('wh-2', $wh2);
        $this->addReference('wh-3', $wh3);
    }

    public function getOrder()
    {
        return 5;
    }
}
