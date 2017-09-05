<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\CertificateRequest;

class LoadCertificateRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $cr1 = new CertificateRequest();
        $cr1->setUser($this->getReference('user-assistant'));

        $manager->persist($cr1);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
