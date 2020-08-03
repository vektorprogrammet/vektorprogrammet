<?php

namespace AppBundle\DataFixtures\ORM;

use DateTime;
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
        $receipt1->setSubmitDate(new DateTime('2016-09-05'));
        $receipt1->setReceiptDate(new DateTime('2016-09-01'));
        $receipt1->setDescription(
            'Kaffetraktere og grenuttak til stand'
        );
        $receipt1->setSum(1108);
        $receipt1->setPicturePath('/images/receipt_images/clas.jpg');
        $manager->persist($receipt1);

        $receipt2 = new Receipt();
        $receipt2->setUser($this->getReference('user-1'));
        $receipt2->setSubmitDate(new DateTime('2017-04-03'));
        $receipt2->setReceiptDate(new DateTime('2017-03-17'));
        $receipt2->setDescription(
            'Taco til Tor'
        );
        $receipt2->setSum(133);
        $receipt2->setPicturePath('/images/receipt_images/taco.jpg');
        $manager->persist($receipt2);

        $receipt3 = new Receipt();
        $receipt3->setUser($this->getReference('user-1'));
        $receipt3->setSubmitDate(new DateTime('2015-11-03'));
        $receipt3->setReceiptDate(new DateTime('2015-10-12'));
        $receipt3->setDescription(
            'Teamsosialt med IT'
        );
        $receipt3->setSum(531);
        $receipt3->setStatus(Receipt::STATUS_REFUNDED);
        $receipt3->setRefundDate(new DateTime('2015-11-08'));
        $receipt3->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receipt3);

        $receipt4 = new Receipt();
        $receipt4->setUser($this->getReference('user-2'));
        $receipt4->setSubmitDate(new DateTime());
        $receipt4->setReceiptDate(new DateTime());
        $receipt4->setDescription(
            'Innkjøp til hyttetur'
        );
        $receipt4->setSum(828.77);
        $receipt4->setPicturePath('/images/receipt_images/hyttetur.jpg');
        $manager->persist($receipt4);

        $receiptAssistant = new Receipt();
        $receiptAssistant->setUser($this->getReference('user-assistant'));
        $receiptAssistant->setSubmitDate(new DateTime('2015-11-03'));
        $receiptAssistant->setReceiptDate(new DateTime('2015-09-21'));
        $receiptAssistant->setDescription(
            'Teamsosialt med IT'
        );
        $receiptAssistant->setSum(531);
        $receiptAssistant->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receiptAssistant);

        $receiptTeam = new Receipt();
        $receiptTeam->setUser($this->getReference('user-team-member'));
        $receiptTeam->setSubmitDate(new DateTime('2015-11-03'));
        $receiptTeam->setReceiptDate(new DateTime('2015-11-02'));
        $receiptTeam->setDescription(
            'Teamsosialt med IT'
        );
        $receiptTeam->setSum(531);
        $receiptTeam->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receiptTeam);

        $receiptAdmin = new Receipt();
        $receiptAdmin->setUser($this->getReference('user-team-leader'));
        $receiptAdmin->setSubmitDate(new DateTime('2015-11-03'));
        $receiptAdmin->setReceiptDate(new DateTime('2015-09-03'));
        $receiptAdmin->setDescription(
            'Teamsosialt med IT'
        );
        $receiptAdmin->setSum(531);
        $receiptAdmin->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receiptAdmin);

        $receiptSuperAdmin = new Receipt();
        $receiptSuperAdmin->setUser($this->getReference('user-admin'));
        $receiptSuperAdmin->setSubmitDate(new DateTime('2015-11-03'));
        $receiptSuperAdmin->setReceiptDate(new DateTime('2015-10-03'));
        $receiptSuperAdmin->setDescription(
            'Teamsosialt med IT'
        );
        $receiptSuperAdmin->setSum(531);
        $receiptSuperAdmin->setPicturePath('/images/receipt_images/teamsosialt.jpg');
        $manager->persist($receiptSuperAdmin);

        $manager->flush();

        $this->addReference('rec-1', $receipt1);
        $this->addReference('rec-2', $receipt2);
        $this->addReference('rec-3', $receipt3);
        $this->addReference('rec-4', $receipt4);
        $this->addReference('rec-assistant', $receiptAssistant);
        $this->addReference('rec-team', $receiptTeam);
        $this->addReference('rec-admin', $receiptAdmin);
        $this->addReference('rec-superadmin', $receiptSuperAdmin);
    }

    public function getOrder()
    {
        return 20;
    }
}
