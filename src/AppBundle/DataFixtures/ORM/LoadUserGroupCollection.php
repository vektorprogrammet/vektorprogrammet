<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;



class LoadUserGroupCollection extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = $this->getReference("user-1");
        $user2 = $this->getReference("user-2");
        $user3 = $this->getReference("user-3");
        $user4 = $this->getReference("user-4");

        $userArray1 = array($user1, $user2);
        $userArray2 = array($user3, $user4);
        $userArray3 = array($user1, $user2);
        $userArray4 = array($user3, $user4);


        $userGroupCollection1 = new UserGroupCollection();
        $userGroupCollection2 = new UserGroupCollection();

        $userGroup1A = new UserGroup();
        $userGroup1A->setName("A");
        $userGroup1A->setUsers($userArray1);

        $userGroup1B = new UserGroup();
        $userGroup1B->setName("B");
        $userGroup1B->setUsers($userArray2);


        $userGroup2A = new UserGroup();
        $userGroup2A->setName("A");
        $userGroup2A->setUsers($userArray3);

        $userGroup2B = new UserGroup();
        $userGroup2B->setName("B");
        $userGroup2B->setUsers($userArray4);


        $userGroup1A->setUserGroupCollection($userGroupCollection1);
        $userGroup1B->setUserGroupCollection($userGroupCollection1);
        $userGroup2A->setUserGroupCollection($userGroupCollection2);
        $userGroup2B->setUserGroupCollection($userGroupCollection2);

        $userGroupCollection1->setName("Brukergruppe 1");
        $userGroupCollection2->setName("Brukergruppe 2");

        $this->addReference('usergroup1A', $userGroup1A);
        $this->addReference('usergroup1B', $userGroup1B);
        $this->addReference('usergroup2A', $userGroup2A);
        $this->addReference('usergroup2B', $userGroup2B);

        $this->addReference('usergroupcollection-1', $userGroupCollection1);
        $this->addReference('usergroupcollection-2', $userGroupCollection2);

        $manager->persist($userGroup1A);
        $manager->persist($userGroup1B);
        $manager->persist($userGroup2A);
        $manager->persist($userGroup2B);
        $manager->persist($userGroupCollection1);
        $manager->persist($userGroupCollection2);
        $manager->flush();


    }

    public function getOrder()
    {
        return 35;
    }
}
