<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\TodoDeadline;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTodoDeadlineData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $todoDeadline = new TodoDeadline();
        $todoDeadline->setSemester($this->getReference('semester-current'));
        $todoDeadline->setDeadDate(new DateTime('+2 week'));
        $todoDeadline->setTodoItem($this->getReference('to-do-item-short-deadline'));
        $manager->persist($todoDeadline);
        $manager->flush();

        $todoDeadline = new TodoDeadline();
        $todoDeadline->setSemester($this->getReference('semester-current'));
        $todoDeadline->setDeadDate(new DateTime('+15 days'));
        $todoDeadline->setTodoItem($this->getReference('to-do-item-almost-short-deadline'));
        $manager->persist($todoDeadline);
        $manager->flush();

        $todoDeadline = new TodoDeadline();
        $todoDeadline->setSemester($this->getReference('semester-current'));
        $todoDeadline->setDeadDate(new DateTime('-1 days'));
        $todoDeadline->setTodoItem($this->getReference('to-do-item-past-deadline'));
        $manager->persist($todoDeadline);
        $manager->flush();


        $todoDeadline = new TodoDeadline();
        $todoDeadline->setSemester($this->getReference('semester-current'));
        $todoDeadline->setDeadDate(new DateTime('+1 week'));
        $todoDeadline->setTodoItem($this->getReference('to-do-item-mandatory-short-deadline'));
        $manager->persist($todoDeadline);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
