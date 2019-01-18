<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TeamMembership;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamMembershipData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-1'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-2'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-2'));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-3'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-2'));
        $tm->setUser($this->getReference('user-12'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $tm->setIsTeamLeader(false);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-4'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-13'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setEndSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $manager->persist($tm);

        $tm2 = new TeamMembership();
        $tm2->setTeam($this->getReference('team-2'));
        $tm2->setUser($this->getReference('user-2'));
        $tm2->setStartSemester($this->getReference('semester-1'));
        $tm2->setPosition($this->getReference('position-2'));
        $manager->persist($tm2);

        $tm3 = new TeamMembership();
        $tm3->setTeam($this->getReference('team-1'));
        $tm3->setUser($this->getReference('user-4'));
        $tm3->setStartSemester($this->getReference('semester-1'));
        $tm3->setEndSemester($this->getReference('semester-1'));
        $tm3->setPosition($this->getReference('position-2'));
        $manager->persist($tm3);

        $tmUserInTeam1 = new TeamMembership();
        $tmUserInTeam1->setTeam($this->getReference('team-1'));
        $tmUserInTeam1->setUser($this->getReference('userInTeam1'));
        $tmUserInTeam1->setStartSemester($this->getReference('semester-1'));
        $tmUserInTeam1->setPosition($this->getReference('position-1'));
        $manager->persist($tmUserInTeam1);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-marte'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-21'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $tm = new TeamMembership();
        $tm->setTeam($this->getReference('team-1'));
        $tm->setUser($this->getReference('user-anna'));
        $tm->setStartSemester($this->getReference('semester-1'));
        $tm->setPosition($this->getReference('position-1'));
        $tm->setIsTeamLeader(true);
        $manager->persist($tm);

        $manager->flush();

        $this->addReference('tm-1', $tm);
        $this->addReference('tm-2', $tm2);
        $this->addReference('tm-3', $tm3);
    }

    public function getOrder()
    {
        return 5;
    }
}
