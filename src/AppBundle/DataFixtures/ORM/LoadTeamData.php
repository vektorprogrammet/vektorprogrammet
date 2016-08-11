<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Team;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $team1 = new Team();
        $team1->setDepartment($this->getReference('dep-1'));
        $team1->setName('Hovedstyret');
        $manager->persist($team1);

        $team2 = new Team();
        $team2->setDepartment($this->getReference('dep-1'));
        $team2->setName('IT');
        $manager->persist($team2);

        $manager->flush();

        $this->addReference('team-1', $team1);
        $this->addReference('team-2', $team2);
    }

    public function getOrder()
    {
        return 3;
    }
}
