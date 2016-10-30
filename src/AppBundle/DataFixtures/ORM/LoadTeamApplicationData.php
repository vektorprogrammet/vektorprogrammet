<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TeamApplication;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamApplicationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $teamApplication1 = new TeamApplication();
        $teamApplication1->setName('Arnt Erik');
        $teamApplication1->setEmail('vektor@vektorprogrammet.no');
        $teamApplication1->setApplicationText('Jeg vil jobbe med TEAM!!!');
        $teamApplication1->setTeam($this->getReference('team-1'));
        $manager->persist($teamApplication1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
