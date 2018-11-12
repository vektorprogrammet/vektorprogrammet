<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ToDoCompleted;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadToDoCompletedData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $toDoCompleted1 = new ToDoCompleted();
        $toDoCompleted1->setSemester($this->getReference('semester-current'));
        $toDoCompleted1->setDepartment($this->getReference('dep-1'));
        $toDoCompleted1->setCompletedAt(new DateTime('2018-05-05'));
        $toDoCompleted1->setToDoItem($this->getReference('completed-to-do-item'));
        $manager->persist($toDoCompleted1);

        $manager->flush();

        $this->setReference('to-do-completed-1', $toDoCompleted1);
    }

    public function getOrder()
    {
        return 6;
    }
}
