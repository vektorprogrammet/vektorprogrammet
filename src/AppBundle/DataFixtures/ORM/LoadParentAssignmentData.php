<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ParentAssignment;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadParentAssignmentData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $parentAssignment1 = new ParentAssignment();
        $parentAssignment1->setNavn('Alexander J Ohrt');
        $parentAssignment1->setEpost('alexa99@hotmail.com');
        $parentAssignment1->setTidspunkt(new \DateTime());
        $parentAssignment1->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment1);

        $parentAssignment2 = new ParentAssignment();
        $parentAssignment2->setNavn('Jonas Sverre Ludvigsen');
        $parentAssignment2->setEpost('JonasSverre@gmail.com');
        $parentAssignment2->setTidspunkt(new \DateTime());
        $parentAssignment2->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment2);

        $parentAssignment3 = new ParentAssignment();
        $parentAssignment3->setNavn('Mathias Johnsen');
        $parentAssignment3->setEpost('Johnsen@vålerenga.osloby.no');
        $parentAssignment3->setTidspunkt(new \DateTime());
        $parentAssignment3->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment3);

        $parentAssignment4 = new ParentAssignment();
        $parentAssignment4->setNavn('Håvard Bråthen');
        $parentAssignment4->setEpost('Br@mæhmed.no');
        $parentAssignment4->setTidspunkt(new \DateTime());
        $parentAssignment4->setCourse($this->getReference('parent-course-2'));

        $manager->persist($parentAssignment4);

        $parentAssignment5 = new ParentAssignment();
        $parentAssignment5->setNavn('Martin Halvorsen');
        $parentAssignment5->setEpost('Br@ad.com');
        $parentAssignment5->setTidspunkt(new \DateTime());
        $parentAssignment5->setCourse($this->getReference('parent-course-2'));

        $manager->persist($parentAssignment5);

        $parentAssignment6 = new ParentAssignment();
        $parentAssignment6->setNavn('Johann Sheitsen');
        $parentAssignment6->setEpost('yes@gull.com');
        $parentAssignment6->setTidspunkt(new \DateTime());
        $parentAssignment6->setCourse($this->getReference('parent-course-3'));

        $manager->persist($parentAssignment6);

        $parentAssignment7 = new ParentAssignment();
        $parentAssignment7->setNavn('Johann Sheitsen');
        $parentAssignment7->setEpost('yes@gull.com');
        $parentAssignment7->setTidspunkt(new \DateTime());
        $parentAssignment7->setCourse($this->getReference('parent-course-3'));

        $manager->persist($parentAssignment7);


        $manager->flush();
    }
}
