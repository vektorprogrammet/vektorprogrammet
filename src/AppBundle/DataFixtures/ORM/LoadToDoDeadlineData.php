<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ToDoDeadline;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadToDoDeadlineData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $toDoDeadline = new ToDoDeadline();
        $toDoDeadline->setSemester($this->getReference('semester-current'));
        $toDoDeadline->setDeadDate(new \DateTime('+2 week'));
        $toDoDeadline->setToDoItem($this->getReference('to-do-item-short-deadline'));
        $manager->persist($toDoDeadline);
        $manager->flush();

        $toDoDeadline = new ToDoDeadline();
        $toDoDeadline->setSemester($this->getReference('semester-current'));
        $toDoDeadline->setDeadDate(new \DateTime('+15 days'));
        $toDoDeadline->setToDoItem($this->getReference('to-do-item-almost-short-deadline'));
        $manager->persist($toDoDeadline);
        $manager->flush();

        $toDoDeadline = new ToDoDeadline();
        $toDoDeadline->setSemester($this->getReference('semester-current'));
        $toDoDeadline->setDeadDate(new \DateTime('-1 days'));
        $toDoDeadline->setToDoItem($this->getReference('to-do-item-past-deadline'));
        $manager->persist($toDoDeadline);
        $manager->flush();


        $toDoDeadline = new ToDoDeadline();
        $toDoDeadline->setSemester($this->getReference('semester-current'));
        $toDoDeadline->setDeadDate(new \DateTime('+1 week'));
        $toDoDeadline->setToDoItem($this->getReference('to-do-item-mandatory-short-deadline'));
        $manager->persist($toDoDeadline);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
