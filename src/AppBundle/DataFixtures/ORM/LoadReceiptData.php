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
        $receipt1->setSubmitDate(new \DateTime());
        $receipt1->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        );
        $receipt1->setSum(120.50);
        $receipt1->setIsActive(true);
        $manager->persist($receipt1);


        $receipt2 = new Receipt();
        $receipt2->setUser($this->getReference('user-1'));
        $receipt2->setSubmitDate(new \DateTime());
        $receipt2->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
        );
        $receipt2->setSum(100);
        $receipt2->setIsActive(true);
        $manager->persist($receipt2);


        $receipt3 = new Receipt();
        $receipt3->setUser($this->getReference('user-1'));
        $receipt3->setSubmitDate(new \DateTime());
        $receipt3->setDescription(
            'Rekrutteringsfylla'
        );
        $receipt3->setSum(7000);
        $receipt3->setIsActive(false);
        $manager->persist($receipt3);


        $receipt4 = new Receipt();
        $receipt4->setUser($this->getReference('user-2'));
        $receipt4->setSubmitDate(new \DateTime());
        $receipt4->setDescription(
            'Vafler på kontoret'
        );
        $receipt4->setSum(130.40);
        $receipt4->setIsActive(true);
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
