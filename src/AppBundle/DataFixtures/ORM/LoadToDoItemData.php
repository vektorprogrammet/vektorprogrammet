<?php

namespace AppBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ToDoItem;
use function GuzzleHttp\Promise\iter_for;

class LoadToDoItemData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $toDoItem1 = new ToDoItem();
        $toDoItem1->setCreatedAt(new DateTime('2018-01-01'));
        $toDoItem1->setTitle("Kaffe");
        $toDoItem1->setDescription("Lag kaffe ved hjelp av kaffemaskinen");
        $toDoItem1->setPriority(1);
        $toDoItem1->setDepartment($this->getReference('dep-1'));
        $toDoItem1->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem1);

        $toDoItem2 = new ToDoItem();
        $toDoItem2->setCreatedAt(new DateTime('2018-01-01'));
        $toDoItem2->setDeletedAt(new DateTime('2018-10-10'));
        $toDoItem2->setTitle("Stand");
        $toDoItem2->setDescription("Rigg opp en pult der store folkemengder ofte samles. Del ut kaffe, snakk med forbipasserende og forsøk å verve nye assistenter. ");
        $toDoItem2->setPriority(2);
        $toDoItem2->setDepartment($this->getReference('dep-2'));
        $toDoItem2->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem2);

        $toDoItem3 = new ToDoItem();
        $toDoItem3->setCreatedAt(new DateTime('2017-01-01'));
        //$toDoItem3->setDeletedAt(new DateTime('2018-12-12'));
        $toDoItem3->setTitle("incompletedToDoItem");
        $toDoItem3->setDescription("This is not completed");
        $toDoItem3->setPriority(3);
        //$toDoItem3->setDepartment($this->getReference('dep-2')); <-- Nå er denne global (for alle departments)
        //$toDoItem3->setSemester($this->getReference('semester-current'));
        $manager->persist($toDoItem3);

        $toDoItem4 = new ToDoItem();
        $toDoItem4->setCreatedAt(new DateTime('2017-01-01'));
        $toDoItem4->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem4->setTitle("deletedToDoItem");
        $toDoItem4->setDescription("Få underskrifter og e-post på interesse-skjema ved stand");
        $toDoItem4->setPriority(5);
        $toDoItem4->setDepartment(null); // Nå er denne global (for alle departments)
        $toDoItem4->setSemester(null); // Nå er denne global (for alle semestre)
        $manager->persist($toDoItem4);

        $toDoItem5 = new ToDoItem();
        $toDoItem5->setCreatedAt(new DateTime('2015-01-01'));
        //$toDoItem5->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem5->setTitle("completedToDoItem");
        $toDoItem5->setDescription("Is completed by Trondheim in høst 2018");
        $toDoItem5->setPriority(5);
        $toDoItem5->setDepartment(null); // Nå er denne global (for alle departments)
        $toDoItem5->setSemester(null); // Nå er denne global (for alle semestre)
        $manager->persist($toDoItem5);

        $toDoItem6 = new ToDoItem();
        $toDoItem6->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem6->setTitle("mandatoryToDoItem");
        $toDoItem6->setDescription("This is mandatory");
        $toDoItem6->setPriority(5);
        $toDoItem6->setDepartment(null);
        $toDoItem6->setSemester(null);
        $manager->persist($toDoItem6);

        $toDoItem7 = new ToDoItem();
        $toDoItem7->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem7->setTitle("shortDeadlineItem");
        $toDoItem7->setDescription("This item has short deadline");
        $toDoItem7->setPriority(5);
        $toDoItem7->setDepartment(null);
        $toDoItem7->setSemester(null);
        $manager->persist($toDoItem7);

        $toDoItem = new ToDoItem();
        $toDoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem->setTitle("almostShortDeadlineItem");
        $toDoItem->setDescription("This item has an almost short deadline");
        $toDoItem->setPriority(5);
        $toDoItem->setDepartment(null);
        $toDoItem->setSemester(null);
        $manager->persist($toDoItem);
        $this->setReference('to-do-item-almost-short-deadline', $toDoItem);

        $toDoItem = new ToDoItem();
        $toDoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem->setTitle("pastDeadlineItem");
        $toDoItem->setDescription("This item has short deadline");
        $toDoItem->setPriority(5);
        $toDoItem->setDepartment(null);
        $toDoItem->setSemester(null);
        $manager->persist($toDoItem);
        $this->setReference('to-do-item-past-deadline', $toDoItem);


        $toDoItem = new ToDoItem();
        $toDoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$toDoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $toDoItem->setTitle("mandatoryShortDeadlineItem");
        $toDoItem->setDescription("This item has short deadline and is mandatory");
        $toDoItem->setPriority(5);
        $toDoItem->setDepartment(null);
        $toDoItem->setSemester(null);
        $manager->persist($toDoItem);
        $this->setReference('to-do-item-mandatory-short-deadline', $toDoItem);

        $manager->flush();

        $this->setReference('to-do-item-1', $toDoItem1);
        $this->setReference('to-do-item-2', $toDoItem2);
        $this->setReference('to-do-item-3', $toDoItem3);
        $this->setReference('completed-to-do-item', $toDoItem5);
        $this->setReference('to-do-item-mandatory', $toDoItem6);
        $this->setReference('outdated-to-do-item', $toDoItem4);
        $this->setReference('to-do-item-short-deadline', $toDoItem7);
    }

    public function getOrder()
    {
        return 5;
    }
}
