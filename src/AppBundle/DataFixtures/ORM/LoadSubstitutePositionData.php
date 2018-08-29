<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SubstitutePosition;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSubstitutePositionData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $sp = new SubstitutePosition();
        $sp->setComment("Kan i ukene 38-45, men ikke 40.");
        $sp->setSemester($this->getReference('semester-current'));
        $sp->setUser($this->getReference('user-1'));
        $sp->setTuesday(false);
        $sp->setThursday(false);
        $this->setReference('substitute-position-1', $sp);
        $manager->persist($sp);

        $sp = new SubstitutePosition();
        $sp->setComment("Liker ikke taco");
        $sp->setSemester($this->getReference('semester-current'));
        $sp->setUser($this->getReference('user-2'));
        $sp->setMonday(false);
        $sp->setTuesday(false);
        $sp->setFriday(false);
        $this->setReference('substitute-position-2', $sp);
        $manager->persist($sp);

        $sp = new SubstitutePosition();
        $sp->setSemester($this->getReference('semester-current'));
        $sp->setUser($this->getReference('user-3'));
        $sp->setThursday(false);
        $sp->setFriday(false);
        $this->setReference('substitute-position-3', $sp);
        $manager->persist($sp);

        $sp = new SubstitutePosition();
        $sp->setSemester($this->getReference('semester-previous'));
        $sp->setUser($this->getReference('user-4'));
        $sp->setTuesday(false);
        $sp->setWednesday(false);
        $sp->setThursday(false);
        $this->setReference('substitute-position-4', $sp);
        $manager->persist($sp);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}
