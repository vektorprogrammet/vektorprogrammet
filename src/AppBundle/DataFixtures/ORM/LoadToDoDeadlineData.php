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
        $toDoDeadline1 = new ToDoDeadline();
        $toDoDeadline1->setSemester($this->getReference('semester-current'));
        $toDoDeadline1->setDeadDate(new \DateTime("2018-09-22"));
        $toDoDeadline1->setToDoItem($this->getReference('to-do-item-2'));
        $manager->persist($toDoDeadline1);
        $manager->flush();

        $this->setReference('to-do-deadline-1', $toDoDeadline1);
    }

    public function getOrder()
    {
        return 6;
    }

}