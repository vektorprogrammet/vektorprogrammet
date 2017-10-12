<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Image;

class LoadImageData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $hyttetur1 = new Image();
        $hyttetur1->setDescription('Fint vær på hytta');
        $hyttetur1->setPath('/images/gallery_images/hyttetur/1.jpg');
        $this->addReference('hyttetur-1', $hyttetur1);
        $manager->persist($hyttetur1);

        $hyttetur2 = new Image();
        $hyttetur2->setDescription('Alltid hyggelig på hytta');
        $hyttetur2->setPath('/images/gallery_images/hyttetur/2.jpg');
        $this->addReference('hyttetur-2', $hyttetur2);
        $manager->persist($hyttetur2);

        $hyttetur3 = new Image();
        $hyttetur3->setDescription('God stemning på hytta');
        $hyttetur3->setPath('/images/gallery_images/hyttetur/3.jpg');
        $this->addReference('hyttetur-3', $hyttetur3);
        $manager->persist($hyttetur3);

        $hyttetur4 = new Image();
        $hyttetur4->setDescription('Bra tempo på hytta');
        $hyttetur4->setPath('/images/gallery_images/hyttetur/4.jpg');
        $this->addReference('hyttetur-4', $hyttetur4);
        $manager->persist($hyttetur4);

        $hyttetur5 = new Image();
        $hyttetur5->setDescription('Skikkelig opplegg på hytta');
        $hyttetur5->setPath('/images/gallery_images/hyttetur/5.jpg');
        $this->addReference('hyttetur-5', $hyttetur5);
        $manager->persist($hyttetur5);

        $hyttetur6 = new Image();
        $hyttetur6->setDescription('Alle har det bra på hytta');
        $hyttetur6->setPath('/images/gallery_images/hyttetur/6.jpg');
        $this->addReference('hyttetur-6', $hyttetur6);
        $manager->persist($hyttetur6);

        $hyttetur7 = new Image();
        $hyttetur7->setDescription('Ingen utelates på hytta');
        $hyttetur7->setPath('/images/gallery_images/hyttetur/7.jpg');
        $this->addReference('hyttetur-7', $hyttetur7);
        $manager->persist($hyttetur7);

        $hyttetur8 = new Image();
        $hyttetur8->setDescription('Alle er blide på hytta');
        $hyttetur8->setPath('/images/gallery_images/hyttetur/8.jpg');
        $this->addReference('hyttetur-8', $hyttetur8);
        $manager->persist($hyttetur8);

        $hyttetur9 = new Image();
        $hyttetur9->setDescription('Ordentlig koselig på hytta');
        $hyttetur9->setPath('/images/gallery_images/hyttetur/9.jpg');
        $this->addReference('hyttetur-9', $hyttetur9);
        $manager->persist($hyttetur9);

        $hyttetur10 = new Image();
        $hyttetur10->setDescription('Liv og røre på hytta');
        $hyttetur10->setPath('/images/gallery_images/hyttetur/10.jpg');
        $this->addReference('hyttetur-10', $hyttetur10);
        $manager->persist($hyttetur10);

        $hyttetur11 = new Image();
        $hyttetur11->setDescription('Alle har det gøy på hytta');
        $hyttetur11->setPath('/images/gallery_images/hyttetur/11.jpg');
        $this->addReference('hyttetur-11', $hyttetur11);
        $manager->persist($hyttetur11);

        $hyttetur12 = new Image();
        $hyttetur12->setDescription('Så gøy det er på hytta');
        $hyttetur12->setPath('/images/gallery_images/hyttetur/12.jpg');
        $this->addReference('hyttetur-12', $hyttetur12);
        $manager->persist($hyttetur12);

        $beste1 = new Image();
        $beste1->setDescription('Flotte folk');
        $beste1->setPath('/images/gallery_images/beste/1.jpg');
        $this->addReference('beste-1', $beste1);
        $manager->persist($beste1);

        $beste2 = new Image();
        $beste2->setDescription('Skikkelig hyggelige folk');
        $beste2->setPath('/images/gallery_images/beste/2.jpg');
        $this->addReference('beste-2', $beste2);
        $manager->persist($beste2);

        $beste3 = new Image();
        $beste3->setDescription('I vektorprogrammet er det bare vennlige mennesker');
        $beste3->setPath('/images/gallery_images/beste/3.jpg');
        $this->addReference('beste-3', $beste3);
        $manager->persist($beste3);

        $beste4 = new Image();
        $beste4->setDescription('Vektorprogrammet er vervet for deg som liker hygge');
        $beste4->setPath('/images/gallery_images/beste/4.jpg');
        $this->addReference('beste-4', $beste4);
        $manager->persist($beste4);

        $beste5 = new Image();
        $beste5->setDescription('Hyggelig stemning i parken i dag');
        $beste5->setPath('/images/gallery_images/beste/5.jpg');
        $this->addReference('beste-5', $beste5);
        $manager->persist($beste5);

        $beste6 = new Image();
        $beste6->setDescription('Vektorprogrammet gjør det igjen');
        $beste6->setPath('/images/gallery_images/beste/6.jpg');
        $this->addReference('beste-6', $beste6);
        $manager->persist($beste6);

        $beste7 = new Image();
        $beste7->setDescription('Koselige mennesker nyter det fine været');
        $beste7->setPath('/images/gallery_images/beste/7.jpg');
        $this->addReference('beste-7', $beste7);
        $manager->persist($beste7);

        $beste8 = new Image();
        $beste8->setDescription('Topp stemning som vanlig');
        $beste8->setPath('/images/gallery_images/beste/8.jpg');
        $this->addReference('beste-8', $beste8);
        $manager->persist($beste8);

        $manager->flush();
    }

    public function getOrder()
    {
        return 21;
    }
}
