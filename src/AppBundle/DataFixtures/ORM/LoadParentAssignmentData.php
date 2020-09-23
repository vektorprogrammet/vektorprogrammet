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
        $parentAssignment1->setName('Alexander J Ohrt');
        $parentAssignment1->setEmail('alexa99@hotmail.com');
        $parentAssignment1->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment1);

        $parentAssignment2 = new ParentAssignment();
        $parentAssignment2->setName('Jonas Sverre Ludvigsen');
        $parentAssignment2->setEmail('JonasSverre@gmail.com');
        $parentAssignment2->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment2);

        $parentAssignment3 = new ParentAssignment();
        $parentAssignment3->setName('Mathias Johnsen');
        $parentAssignment3->setEmail('Johnsen@vålerenga.osloby.no');
        $parentAssignment3->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment3);

        $parentAssignment4 = new ParentAssignment();
        $parentAssignment4->setName('Håvard Bråthen');
        $parentAssignment4->setEmail('Br@mæhmed.no');
        $parentAssignment4->setCourse($this->getReference('parent-course-2'));

        $manager->persist($parentAssignment4);

        $parentAssignment5 = new ParentAssignment();
        $parentAssignment5->setName('Martin Halvorsen');
        $parentAssignment5->setEmail('Br@ad.com');
        $parentAssignment5->setCourse($this->getReference('parent-course-2'));

        $manager->persist($parentAssignment5);

        $parentAssignment6 = new ParentAssignment();
        $parentAssignment6->setName('Johann Sheitsen');
        $parentAssignment6->setEmail('yes@gull.com');
        $parentAssignment6->setCourse($this->getReference('parent-course-3'));

        $manager->persist($parentAssignment6);

        $parentAssignment7 = new ParentAssignment();
        $parentAssignment7->setName('Johanna Jensrud');
        $parentAssignment7->setEmail('yes@yahoo.com');
        $parentAssignment7->setCourse($this->getReference('parent-course-3'));

        $manager->persist($parentAssignment7);

        $manager->flush();
    }
}
