<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $role1 = new Role();
        $role1->setName('Bruker');
        $role1->setRole('ROLE_USER');
        $manager->persist($role1);

        $role2 = new Role();
        $role2->setName('Teammedlem');
        $role2->setRole('ROLE_TEAM_MEMBER');
        $manager->persist($role2);

        $role3 = new Role();
        $role3->setName('Teamleder');
        $role3->setRole('ROLE_TEAM_LEADER');
        $manager->persist($role3);

        $role4 = new Role();
        $role4->setName('Admin');
        $role4->setRole('ROLE_ADMIN');
        $manager->persist($role4);

        $manager->flush();

        $this->addReference('role-1', $role1);
        $this->addReference('role-2', $role2);
        $this->addReference('role-3', $role3);
        $this->addReference('role-4', $role4);
    }

    public function getOrder()
    {
        return 3;
    }
}
