<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Fixture as AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use AppBundle\Entity\CertificateRequest;

class LoadCertificateRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cr1 = new CertificateRequest();
        $cr1->setUser($this->getReference('user-assistant'));

        $manager->persist($cr1);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
