<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Position;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPositionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $position1 = new Position();
        $position1->setName('Leder');
        $manager->persist($position1);

        $position2 = new Position();
        $position2->setName('Medlem');
        $manager->persist($position2);

        $manager->flush();

        $this->addReference('position-1', $position1);
        $this->addReference('position-2', $position2);
    }

    public function getOrder()
    {
        return 1;
    }
}
