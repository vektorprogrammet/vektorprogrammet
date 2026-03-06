<?php

namespace App\DataFixtures\ORM;

use App\Entity\CertificateRequest;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadCertificateRequestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cr1 = new CertificateRequest();
        $cr1->setUser($this->getReference('user-assistant', User::class));

        $manager->persist($cr1);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
