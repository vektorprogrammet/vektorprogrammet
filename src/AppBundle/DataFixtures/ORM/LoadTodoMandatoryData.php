<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TodoMandatory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTodoMandatoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $todoMandatory1 = new TodoMandatory();
        $todoMandatory1->setSemester($this->getReference('semester-current'));
        $todoMandatory1->setIsMandatory(true);
        $todoMandatory1->setTodoItem($this->getReference('to-do-item-2'));
        $manager->persist($todoMandatory1);
        $manager->flush();

        $todoMandatory2 = new TodoMandatory();
        $todoMandatory2->setSemester($this->getReference('semester-3'));
        $todoMandatory2->setIsMandatory(true);
        $todoMandatory2->setTodoItem($this->getReference('to-do-item-1'));
        $manager->persist($todoMandatory2);
        $manager->flush();

        $todoMandatory3 = new TodoMandatory();
        $todoMandatory3->setSemester($this->getReference('semester-2'));
        $todoMandatory3->setIsMandatory(false);
        $todoMandatory3->setTodoItem($this->getReference('to-do-item-1'));
        $manager->persist($todoMandatory3);
        $manager->flush();

        $todoMandatory4 = new TodoMandatory();
        $todoMandatory4->setSemester($this->getReference('semester-current'));
        $todoMandatory4->setIsMandatory(true);
        $todoMandatory4->setTodoItem($this->getReference('to-do-item-mandatory-short-deadline'));
        $manager->persist($todoMandatory4);
        $manager->flush();

        $todoMandatory4 = new TodoMandatory();
        $todoMandatory4->setSemester($this->getReference('semester-current'));
        $todoMandatory4->setIsMandatory(true);
        $todoMandatory4->setTodoItem($this->getReference('to-do-item-mandatory'));
        $manager->persist($todoMandatory4);
        $manager->flush();

        $this->setReference('to-do-mandatory-1', $todoMandatory1);
        $this->setReference('to-do-mandatory-2', $todoMandatory2);
        $this->setReference('to-do-mandatory-3', $todoMandatory3);
    }

    public function getOrder()
    {
        return 6;
    }
}
