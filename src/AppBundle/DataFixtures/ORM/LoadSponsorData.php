<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Sponsor;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSponsorData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $sponsor = new Sponsor();
        $sponsor->setName('NTNU');
        $sponsor->setLogoImagePath('/images/Logo images/56bbb243277567.54059788');
        $sponsor->setUrl('http://www.ntnu.no/');
        $sponsor->setSize('large');
        $manager->persist($sponsor);

        $sponsor = new Sponsor();
        $sponsor->setName('Surnadal Sparebank');
        $sponsor->setLogoImagePath('/images/Logo images/56bbb0ebeb6950.06298651');
        $sponsor->setUrl('https://bank.no/privat');
        $manager->persist($sponsor);

        $sponsor = new Sponsor();
        $sponsor->setName('Samarbeidsforum');
        $sponsor->setLogoImagePath('/images/Logo images/55c861b289bcb2.31450842');
        $sponsor->setUrl('http://www.ntnu.no/nt/sf');
        $sponsor->setSize('medium');
        $manager->persist($sponsor);

        $sponsor = new Sponsor();
        $sponsor->setName('Matematikksenteret');
        $sponsor->setLogoImagePath('/images/Logo images/55c85fbf165705.45260504');
        $sponsor->setUrl('http://www.matematikksenteret.no/');
        $manager->persist($sponsor);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
