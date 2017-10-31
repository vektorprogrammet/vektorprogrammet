<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ImageGallery;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadImageGalleryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $hyttetur = new ImageGallery();
        $hyttetur->setTitle('Hyttetur våren 2017');
        $hyttetur->setDescription('Vektorprogrammets årlige hyttetur for teammedlemmer');
        $hyttetur->setReferenceName('hyttetur');
        $hyttetur->setFilters(array('max_500px' => true, 'article_crop' => true));
        $hyttetur->addImage($this->getReference('hyttetur-1'));
        $hyttetur->addImage($this->getReference('hyttetur-2'));
        $hyttetur->addImage($this->getReference('hyttetur-3'));
        $hyttetur->addImage($this->getReference('hyttetur-4'));
        $hyttetur->addImage($this->getReference('hyttetur-5'));
        $hyttetur->addImage($this->getReference('hyttetur-6'));
        $hyttetur->addImage($this->getReference('hyttetur-7'));
        $hyttetur->addImage($this->getReference('hyttetur-8'));
        $hyttetur->addImage($this->getReference('hyttetur-9'));
        $hyttetur->addImage($this->getReference('hyttetur-10'));
        $hyttetur->addImage($this->getReference('hyttetur-11'));
        $hyttetur->addImage($this->getReference('hyttetur-12'));
        $manager->persist($hyttetur);

        $beste = new ImageGallery();
        $beste->setTitle('Vektorprogrammets beste bilder');
        $beste->setDescription('Kremen av kremen');
        $beste->setReferenceName('beste');
        $beste->setFilters(array('max_500px' => true));
        $beste->addImage($this->getReference('beste-1'));
        $beste->addImage($this->getReference('beste-2'));
        $beste->addImage($this->getReference('beste-3'));
        $beste->addImage($this->getReference('beste-4'));
        $beste->addImage($this->getReference('beste-5'));
        $beste->addImage($this->getReference('beste-6'));
        $beste->addImage($this->getReference('beste-7'));
        $beste->addImage($this->getReference('beste-8'));
        $manager->persist($beste);

        $manager->flush();
    }

    public function getOrder()
    {
        return 22;
    }
}
