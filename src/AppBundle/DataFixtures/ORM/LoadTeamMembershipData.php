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
        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-1'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $wh->setIsTeamLeader(true);
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-2'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-2'));
        $wh->setIsTeamLeader(true);
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-3'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-2'));
        $wh->setUser($this->getReference('user-12'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $wh->setIsTeamLeader(false);
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-4'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-13'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setEndSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $manager->persist($wh);

        $wh2 = new TeamMembership();
        $wh2->setTeam($this->getReference('team-2'));
        $wh2->setUser($this->getReference('user-2'));
        $wh2->setStartSemester($this->getReference('semester-1'));
        $wh2->setPosition($this->getReference('position-2'));
        $manager->persist($wh2);

        $wh3 = new TeamMembership();
        $wh3->setTeam($this->getReference('team-1'));
        $wh3->setUser($this->getReference('user-4'));
        $wh3->setStartSemester($this->getReference('semester-1'));
        $wh3->setEndSemester($this->getReference('semester-1'));
        $wh3->setPosition($this->getReference('position-2'));
        $manager->persist($wh3);

        $whUserInTeam1 = new TeamMembership();
        $whUserInTeam1->setTeam($this->getReference('team-1'));
        $whUserInTeam1->setUser($this->getReference('userInTeam1'));
        $whUserInTeam1->setStartSemester($this->getReference('semester-1'));
        $whUserInTeam1->setPosition($this->getReference('position-1'));
        $manager->persist($whUserInTeam1);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-marte'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $wh->setIsTeamLeader(true);
        $manager->persist($wh);

        $wh = new TeamMembership();
        $wh->setTeam($this->getReference('team-1'));
        $wh->setUser($this->getReference('user-anna'));
        $wh->setStartSemester($this->getReference('semester-1'));
        $wh->setPosition($this->getReference('position-1'));
        $wh->setIsTeamLeader(true);
        $manager->persist($wh);

        $manager->flush();

        $this->addReference('wh-1', $wh);
        $this->addReference('wh-2', $wh2);
        $this->addReference('wh-3', $wh3);
    }

    public function getOrder()
    {
        return 5;
    }
}
