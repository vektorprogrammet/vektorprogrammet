<?php

namespace AppBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ToDoItem;

class LoadToDoItemData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $toDoItem1 = new ToDoItem();
        $toDoItem1->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem1->setDeletedAt(new DateTime('2018-09-09'));
        $toDoItem1->setTitle("Jobbejuice");
        $toDoItem1->setDescription("Lag kaffe ved hjelp av kaffemaskinen");
        $toDoItem1->setPriority(1);
        $toDoItem1->setDepartment($this->getReference('dep-1'));
        $toDoItem1->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem1);

        $toDoItem2 = new ToDoItem();
        $toDoItem2->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem2->setDeletedAt(new DateTime('2018-12-12'));
        $toDoItem2->setTitle("Stand");
        $toDoItem2->setDescription("Rigg opp en pult der store folkemengder ofte samles. Del ut kaffe, snakk med forbipasserende og forsøk å verve nye assistenter. ");
        $toDoItem2->setPriority(2);
        $toDoItem2->setDepartment($this->getReference('dep-2'));
        $toDoItem2->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem2);

        $toDoItem3 = new ToDoItem();
        $toDoItem3->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem3->setDeletedAt(new DateTime('2018-12-12'));
        $toDoItem3->setTitle("Blesting");
        $toDoItem3->setDescription("I pausene mellom forelesning, spør om å låne mikrofonen til foreleseren for å fortelle salen litt om vektorprogrammet, hva det innebærer og hvorfor det er gøy. Skriv så opp URL til vektoprogrammets nettside.");
        $toDoItem3->setPriority(3);
        //$toDoItem3->setDepartment($this->getReference('dep-2')); <-- Nå er denne global (for alle departments)
        $toDoItem3->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem3);

        $toDoItem4 = new ToDoItem();
        $toDoItem4->setCreatedAt(new DateTime('2015-01-01'));
        //$toDoItem4->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem4->setTitle("Få underskrifter");
        $toDoItem4->setDescription("Få underskrifter og e-post på interesse-skjema ved stand");
        $toDoItem4->setPriority(5);
        $toDoItem4->setDepartment(null); // Nå er denne global (for alle departments)
        $toDoItem4->setSemester(null); // Nå er denne global (for alle semestre)
        $manager->persist($toDoItem4);

        $manager->flush();

        $this->setReference('to-do-item-1', $toDoItem1);
        $this->setReference('to-do-item-2', $toDoItem2);
        $this->setReference('to-do-item-3', $toDoItem3);
        $this->setReference('outdated-to-do-item', $toDoItem4);
    }

    public function getOrder()
    {
        return 5;
    }
}
