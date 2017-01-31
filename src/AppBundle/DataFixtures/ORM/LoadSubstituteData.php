<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Substitute;

class LoadSubstituteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sub0 = new Substitute();
        $sub0->setApplication($this->getReference('application-0'));
        $manager->persist($sub0);

        $sub1 = new Substitute();
        $sub1->setApplication($this->getReference('application-1'));
        $manager->persist($sub1);

        $sub2 = new Substitute();
        $sub2->setApplication($this->getReference('application-2'));
        $manager->persist($sub2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
