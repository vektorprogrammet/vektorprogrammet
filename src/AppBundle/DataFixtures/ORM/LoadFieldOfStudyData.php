<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\FieldOfStudy;

class LoadFieldOfStudyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fos1 = new FieldOfStudy();
        $fos1->setName('Bachelor i informatikk');
        $fos1->setShortName('BIT');
        $fos1->setDepartment($this->getReference('dep-1'));
        $manager->persist($fos1);

        $fos2 = new FieldOfStudy();
        $fos2->setName('Datateknologi');
        $fos2->setShortName('MIDT');
        $fos2->setDepartment($this->getReference('dep-1'));
        $manager->persist($fos2);

        $fos3 = new FieldOfStudy();
        $fos3->setName('Bachelor i økonomi og administrasjon');
        $fos3->setShortName('BITA');
        $fos3->setDepartment($this->getReference('dep-2'));
        $manager->persist($fos3);

        $fos4 = new FieldOfStudy();
        $fos4->setName('Miljøfysikk og fornybar energi');
        $fos4->setShortName('MFE');
        $fos4->setDepartment($this->getReference('dep-3'));
        $manager->persist($fos4);

        $fos5 = new FieldOfStudy();
        $fos5->setName('Matematikk og økonomi (bachelor)');
        $fos5->setShortName('MAEC');
        $fos5->setDepartment($this->getReference('dep-4'));
        $manager->persist($fos5);

        $manager->flush();

        $this->addReference('fos-1', $fos1);
        $this->addReference('fos-2', $fos2);
        $this->addReference('fos-3', $fos3);
        $this->addReference('fos-4', $fos4);
        $this->addReference('fos-5', $fos5);
    }

    public function getOrder()
    {
        return 3;
    }
}