<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TodoCompleted;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTodoCompletedData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $todoCompleted1 = new TodoCompleted();
        $todoCompleted1->setSemester($this->getReference('semester-current'));
        $todoCompleted1->setDepartment($this->getReference('dep-1'));
        $todoCompleted1->setCompletedAt(new DateTime('2018-05-05'));
        $todoCompleted1->setTodoItem($this->getReference('completed-to-do-item'));
        $manager->persist($todoCompleted1);

        $manager->flush();

        $this->setReference('to-do-completed-1', $todoCompleted1);
    }

    public function getOrder()
    {
        return 6;
    }
}
