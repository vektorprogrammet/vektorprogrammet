<?php

namespace App\DataFixtures\ORM;

use App\Entity\Department;
use App\Entity\Semester;
use App\Entity\Team;
use App\Entity\TeamInterest;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadTeamInterestData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $teamInterest1 = new TeamInterest();
        $teamInterest1
            ->setDepartment($this->getReference('dep-1', Department::class))
            ->setSemester($this->getReference('semester-current', Semester::class))
            ->setPotentialTeams([
                $this->getReference('team-1', Team::class),
                $this->getReference('team-2', Team::class), ])
            ->setName('Magnus Carlsen')
            ->setEmail('magnus@gmail.com');
        $manager->persist($teamInterest1);
        $this->addReference('team-interest-1', $teamInterest1);

        $teamInterest2 = new TeamInterest();
        $teamInterest2
            ->setDepartment($this->getReference('dep-1', Department::class))
            ->setSemester($this->getReference('semester-previous', Semester::class))
            ->setPotentialTeams([$this->getReference('team-1', Team::class)])
            ->setName('Morten Nome')
            ->setEmail('nome@ntnu.no');
        $manager->persist($teamInterest2);
        $this->addReference('team-interest-2', $teamInterest2);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }
}
