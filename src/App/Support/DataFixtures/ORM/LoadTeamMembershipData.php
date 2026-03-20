<?php

namespace App\Support\DataFixtures\ORM;

use App\Organization\Infrastructure\Entity\Position;
use App\Shared\Entity\Semester;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadTeamMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-1', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-2', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-2', Position::class));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-3', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-2', Team::class));
        $tm->setUser($this->getReference('user-12', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $tm->setIsTeamLeader(false);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-4', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-13', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setEndSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $manager->persist($tm);

        $tm2 = new TeamMembership();
        $tm2->setTeam($this->getReference('team-2', Team::class));
        $tm2->setUser($this->getReference('user-2', User::class));
        $tm2->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm2->setPosition($this->getReference('position-2', Position::class));
        $manager->persist($tm2);

        $tm3 = new TeamMembership();
        $tm3->setTeam($this->getReference('team-1', Team::class));
        $tm3->setUser($this->getReference('user-4', User::class));
        $tm3->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm3->setEndSemester($this->getReference('semester-1', Semester::class));
        $tm3->setPosition($this->getReference('position-2', Position::class));
        $manager->persist($tm3);

        $tmUserInTeam1 = new TeamMembership();
        $tmUserInTeam1->setTeam($this->getReference('team-1', Team::class));
        $tmUserInTeam1->setUser($this->getReference('userInTeam1', User::class));
        $tmUserInTeam1->setStartSemester($this->getReference('semester-1', Semester::class));
        $tmUserInTeam1->setPosition($this->getReference('position-1', Position::class));
        $manager->persist($tmUserInTeam1);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-marte', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-21', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1', Team::class));
        $tm->setUser($this->getReference('user-anna', User::class));
        $tm->setStartSemester($this->getReference('semester-1', Semester::class));
        $tm->setPosition($this->getReference('position-1', Position::class));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $manager->flush();

        $this->addReference('tm-1', $tm);
        $this->addReference('tm-2', $tm2);
        $this->addReference('tm-3', $tm3);
    }

    public function getOrder(): int
    {
        return 5;
    }
}
