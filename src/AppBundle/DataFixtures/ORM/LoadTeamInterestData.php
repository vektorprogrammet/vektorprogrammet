<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TeamInterest;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamInterestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $teamInterest1 = new TeamInterest();
        $teamInterest1
            ->setSemester($this->getReference('semester-current'))
            ->setPotentialTeams(array(
                $this->getReference('team-1'),
                $this->getReference('team-2'),))
            ->setName('Magnus Carlsen')
            ->setEmail('magnus@gmail.com');
        $manager->persist($teamInterest1);
        $this->addReference('team-interest-1', $teamInterest1);

        $teamInterest2 = new TeamInterest();
        $teamInterest2
            ->setSemester($this->getReference('semester-previous'))
            ->setPotentialTeams(array($this->getReference('team-1')))
            ->setName('Morten Nome')
            ->setEmail('nome@ntnu.no');
        $manager->persist($teamInterest2);
        $this->addReference('team-interest-2', $teamInterest2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
