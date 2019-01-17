<?php

namespace AppBundle\DataFixtures\ORM;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\TodoItem;
use function GuzzleHttp\Promise\iter_for;

class LoadTodoItemData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $todoItem1 = new TodoItem();
        $todoItem1->setCreatedAt(new DateTime('2018-01-01'));
        $todoItem1->setTitle("Kaffe");
        $todoItem1->setDescription("Lag kaffe ved hjelp av kaffemaskinen");
        $todoItem1->setPriority(1);
        $todoItem1->setDepartment($this->getReference('dep-1'));
        $todoItem1->setSemester($this->getReference('semester-current'));
        $manager->persist($todoItem1);

        $todoItem2 = new TodoItem();
        $todoItem2->setCreatedAt(new DateTime('2018-01-01'));
        $todoItem2->setDeletedAt(new DateTime('2018-10-10'));
        $todoItem2->setTitle("Stand");
        $todoItem2->setDescription("Rigg opp en pult der store folkemengder ofte samles. Del ut kaffe, snakk med forbipasserende og forsøk å verve nye assistenter. ");
        $todoItem2->setPriority(2);
        $todoItem2->setDepartment($this->getReference('dep-2'));
        $todoItem2->setSemester($this->getReference('semester-current'));
        $manager->persist($todoItem2);

        $todoItem3 = new TodoItem();
        $todoItem3->setCreatedAt(new DateTime('2017-01-01'));
        //$todoItem3->setDeletedAt(new DateTime('2018-12-12'));
        $todoItem3->setTitle("incompletedTodoItem");
        $todoItem3->setDescription("This is not completed");
        $todoItem3->setPriority(3);
        //$todoItem3->setDepartment($this->getReference('dep-2')); <-- Nå er denne global (for alle departments)
        //$todoItem3->setSemester($this->getReference('semester-current'));
        $manager->persist($todoItem3);

        $todoItem4 = new TodoItem();
        $todoItem4->setCreatedAt(new DateTime('2017-01-01'));
        $todoItem4->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem4->setTitle("deletedTodoItem");
        $todoItem4->setDescription("Få underskrifter og e-post på interesse-skjema ved stand");
        $todoItem4->setPriority(5);
        $todoItem4->setDepartment(null); // Nå er denne global (for alle departments)
        $todoItem4->setSemester(null); // Nå er denne global (for alle semestre)
        $manager->persist($todoItem4);

        $todoItem5 = new TodoItem();
        $todoItem5->setCreatedAt(new DateTime('2015-01-01'));
        //$todoItem5->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem5->setTitle("completedTodoItem");
        $todoItem5->setDescription("Is completed by Trondheim in the current semester");
        $todoItem5->setPriority(5);
        $todoItem5->setDepartment(null); // Nå er denne global (for alle departments)
        $todoItem5->setSemester(null); // Nå er denne global (for alle semestre)
        $manager->persist($todoItem5);

        $todoItem6 = new TodoItem();
        $todoItem6->setCreatedAt(new DateTime('2018-01-01'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem6->setTitle("mandatoryTodoItem");
        $todoItem6->setDescription("This is mandatory");
        $todoItem6->setPriority(5);
        $todoItem6->setDepartment(null);
        $todoItem6->setSemester(null);
        $manager->persist($todoItem6);

        $todoItem7 = new TodoItem();
        $todoItem7->setCreatedAt(new DateTime('2018-01-01'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem7->setTitle("shortDeadlineItem");
        $todoItem7->setDescription("This item has short deadline");
        $todoItem7->setPriority(5);
        $todoItem7->setDepartment(null);
        $todoItem7->setSemester(null);
        $manager->persist($todoItem7);

        $todoItem = new TodoItem();
        $todoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem->setTitle("almostShortDeadlineItem");
        $todoItem->setDescription("This item has an almost short deadline");
        $todoItem->setPriority(5);
        $todoItem->setDepartment(null);
        $todoItem->setSemester(null);
        $manager->persist($todoItem);
        $this->setReference('to-do-item-almost-short-deadline', $todoItem);

        $todoItem = new TodoItem();
        $todoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem->setTitle("pastDeadlineItem");
        $todoItem->setDescription("This item is PAST its deadline");
        $todoItem->setPriority(5);
        $todoItem->setDepartment(null);
        $todoItem->setSemester(null);
        $manager->persist($todoItem);
        $this->setReference('to-do-item-past-deadline', $todoItem);


        $todoItem = new TodoItem();
        $todoItem->setCreatedAt(new DateTime('2018-01-01'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem->setTitle("mandatoryShortDeadlineItem");
        $todoItem->setDescription("This item has short deadline and is mandatory");
        $todoItem->setPriority(5);
        $todoItem->setDepartment(null);
        $todoItem->setSemester(null);
        $manager->persist($todoItem);
        $this->setReference('to-do-item-mandatory-short-deadline', $todoItem);

        $manager->flush();

        $todoItem = new TodoItem();
        $todoItem->setCreatedAt(new DateTime('2018-06-06'));
        //$todoItem6->setDeletedAt(new DateTime('2017-12-12'));
        $todoItem->setTitle("itemToBeDeleted");
        $todoItem->setDescription("This item is deleted in tests");
        $todoItem->setPriority(3);
        $todoItem->setDepartment(null);
        $todoItem->setSemester(null);
        $manager->persist($todoItem);
        $this->setReference('to-do-item-to-be-deleted', $todoItem);

        $manager->flush();

        $this->setReference('to-do-item-1', $todoItem1);
        $this->setReference('to-do-item-2', $todoItem2);
        $this->setReference('to-do-item-3', $todoItem3);
        $this->setReference('completed-to-do-item', $todoItem5);
        $this->setReference('to-do-item-mandatory', $todoItem6);
        $this->setReference('outdated-to-do-item', $todoItem4);
        $this->setReference('to-do-item-short-deadline', $todoItem7);
    }

    public function getOrder()
    {
        return 5;
    }
}
