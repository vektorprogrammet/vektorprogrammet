<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Subscriber;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubscriberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $subscriber = new Subscriber();
        $subscriber->setName('Kong Harald');
        $subscriber->setEmail('harald@rex.no');
        $subscriber->setNewsletter($this->getReference('newsletter-interesseliste'));
        $subscriber->setUnsubscribeCode('123123');
        $manager->persist($subscriber);

        $subscriber2 = new Subscriber();
        $subscriber2->setName('Donald Trump');
        $subscriber2->setEmail('trump@president.com');
        $subscriber2->setNewsletter($this->getReference('newsletter-testliste'));
        $subscriber2->setUnsubscribeCode('12341234');
        $manager->persist($subscriber2);

        $manager->flush();

        $this->addReference('subscriber', $subscriber);
        $this->addReference('subscriber2', $subscriber2);
    }

    public function getOrder()
    {
        return 4;
    }
}
