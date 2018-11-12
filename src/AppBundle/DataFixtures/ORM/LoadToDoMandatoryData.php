<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ToDoMandatory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadToDoMandatoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $toDoMandatory1 = new ToDoMandatory();
        $toDoMandatory1->setSemester($this->getReference('semester-current'));
        $toDoMandatory1->setIsMandatory(true);
        $toDoMandatory1->setToDoItem($this->getReference('to-do-item-2'));
        $manager->persist($toDoMandatory1);
        $manager->flush();

        $toDoMandatory2 = new ToDoMandatory();
        $toDoMandatory2->setSemester($this->getReference('semester-3'));
        $toDoMandatory2->setIsMandatory(true);
        $toDoMandatory2->setToDoItem($this->getReference('to-do-item-1'));
        $manager->persist($toDoMandatory2);
        $manager->flush();

        $toDoMandatory3 = new ToDoMandatory();
        $toDoMandatory3->setSemester($this->getReference('semester-2'));
        $toDoMandatory3->setIsMandatory(false);
        $toDoMandatory3->setToDoItem($this->getReference('to-do-item-1'));
        $manager->persist($toDoMandatory3);
        $manager->flush();

        $toDoMandatory4 = new ToDoMandatory();
        $toDoMandatory4->setSemester($this->getReference('semester-current'));
        $toDoMandatory4->setIsMandatory(true);
        $toDoMandatory4->setToDoItem($this->getReference('to-do-item-mandatory-short-deadline'));
        $manager->persist($toDoMandatory4);
        $manager->flush();

        $toDoMandatory4 = new ToDoMandatory();
        $toDoMandatory4->setSemester($this->getReference('semester-current'));
        $toDoMandatory4->setIsMandatory(true);
        $toDoMandatory4->setToDoItem($this->getReference('to-do-item-mandatory'));
        $manager->persist($toDoMandatory4);
        $manager->flush();

        $this->setReference('to-do-mandatory-1', $toDoMandatory1);
        $this->setReference('to-do-mandatory-2', $toDoMandatory2);
        $this->setReference('to-do-mandatory-3', $toDoMandatory3);
    }

    public function getOrder()
    {
        return 6;
    }
}
