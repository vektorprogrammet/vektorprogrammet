<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ExecutiveBoardMember;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadExecutiveBoardMemberData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $boardMember = new ExecutiveBoardMember();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPosition('Leder');
        $boardMember->setUser($this->getReference('user-20'));
        $boardMember->setStartSemester($this->getReference('semester-1'));
        $boardMember->setEndSemester($this->getReference('semester-2'));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMember();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPosition('Medlem');
        $boardMember->setUser($this->getReference('user-10'));
        $boardMember->setStartSemester($this->getReference('semester-3'));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMember();
        $boardMember->setBoard($this->getReference('board'));
        $boardMember->setPosition('Medlem');
        $boardMember->setUser($this->getReference('user-angela'));
        $boardMember->setStartSemester($this->getReference('semester-previous'));
        $boardMember->setEndSemester($this->getReference('semester-current'));
        $manager->persist($boardMember);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
