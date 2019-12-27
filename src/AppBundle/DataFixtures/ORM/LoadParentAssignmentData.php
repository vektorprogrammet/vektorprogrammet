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
        $parentAssignment = new ParentAssignment();
        $parentAssignment->setNavn('Alexander J Ohrt');
        $parentAssignment->setEpost('alexa99@hotmail.com');
        $parentAssignment->setTidspunkt(new \DateTime());
        $parentAssignment->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment);

        $parentAssignment = new ParentAssignment();
        $parentAssignment->setNavn('Jonas Sverre Ludvigsen');
        $parentAssignment->setEpost('JonasSverre@gmail.com');
        $parentAssignment->setTidspunkt(new \DateTime());
        $parentAssignment->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment);

        $parentAssignment = new ParentAssignment();
        $parentAssignment->setNavn('Mathias Johnsen');
        $parentAssignment->setEpost('Johnsen@vålerenga.osloby.no');
        $parentAssignment->setTidspunkt(new \DateTime());
        $parentAssignment->setCourse($this->getReference('parent-course-1'));

        $manager->persist($parentAssignment);

        $manager->flush();
    }
}
