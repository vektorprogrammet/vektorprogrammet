<?php

namespace App\DataFixtures\ORM;

use App\Entity\ExecutiveBoard;
use App\Entity\ExecutiveBoardMembership;
use App\Shared\Entity\Semester;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadExecutiveBoardMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board', ExecutiveBoard::class));
        $boardMember->setPositionName('Leder');
        $boardMember->setUser($this->getReference('user-20', User::class));
        $boardMember->setStartSemester($this->getReference('semester-1', Semester::class));
        $boardMember->setEndSemester($this->getReference('semester-2', Semester::class));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board', ExecutiveBoard::class));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('user-10', User::class));
        $boardMember->setStartSemester($this->getReference('semester-3', Semester::class));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board', ExecutiveBoard::class));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('user-angela', User::class));
        $boardMember->setStartSemester($this->getReference('semester-previous', Semester::class));
        $boardMember->setEndSemester($this->getReference('semester-current', Semester::class));
        $manager->persist($boardMember);

        $boardMember = new ExecutiveBoardMembership();
        $boardMember->setBoard($this->getReference('board', ExecutiveBoard::class));
        $boardMember->setPositionName('Medlem');
        $boardMember->setUser($this->getReference('userInTeam1', User::class));
        $boardMember->setStartSemester($this->getReference('semester-previous', Semester::class));
        $manager->persist($boardMember);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
