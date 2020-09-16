<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AdmissionSubscriber;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdmissionSubscriberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 365; $i++) {
            $date = new DateTime("-$i days");
            for ($j = 0; $j < rand(1, 10); $j++) {
                $subscriber = new AdmissionSubscriber();
                $subscriber->setDepartment($this->getReference('dep-1'));
                $subscriber->setTimestamp($date);
                $subscriber->setEmail("sub$i.$j@vektorprogrammet.no");
                $manager->persist($subscriber);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
