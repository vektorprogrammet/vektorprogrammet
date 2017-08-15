<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Receipt;

class LoadReceiptData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $receipt1 = new Receipt();
        $receipt1->setUser($this->getReference('user-1'));
        $receipt1->setSubmitDate(new \DateTime('2016-09-05'));
        $receipt1->setDescription(
            'Kaffetraktere og grenuttak til stand'
        );
        $receipt1->setSum(1108);
        $receipt1->setActive(true);
        $receipt1->setPicturePath('/images/receipt_images/clas.jpg');
        $manager->persist($receipt1);

        $receipt2 = new Receipt();
        $receipt2->setUser($this->getReference('user-1'));
        $receipt2->setSubmitDate(new \DateTime('2017-04-03'));
        $receipt2->setDescription(
            'Taco til Tor'
        );
        $receipt2->setSum(133);
        $receipt2->setActive(true);
        $receipt2->setPicturePath('/images/receipt_images/taco.jpg');
        $manager->persist($receipt2);

        $receipt3 = new Receipt();
        $receipt3->setUser($this->getReference('user-1'));
        $receipt3->setSubmitDate(new \DateTime('2015-11-03'));
        $receipt3->setDescription(
            'Teamsosialt med IT'
        );
        $receipt3->setSum(531);
        $receipt3->setActive(false);
        $receipt3->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receipt3);

        $receipt4 = new Receipt();
        $receipt4->setUser($this->getReference('user-2'));
        $receipt4->setSubmitDate(new \DateTime());
        $receipt4->setDescription(
            'Innkjøp til hyttetur'
        );
        $receipt4->setSum(828.77);
        $receipt4->setActive(true);
        $receipt4->setPicturePath('/images/receipt_images/hyttetur.jpg');
        $manager->persist($receipt4);

        $manager->flush();

        $this->addReference('rec-1', $receipt1);
        $this->addReference('rec-2', $receipt2);
        $this->addReference('rec-3', $receipt3);
        $this->addReference('rec-4', $receipt4);
    }

    public function getOrder()
    {
        return 20;
    }
}
