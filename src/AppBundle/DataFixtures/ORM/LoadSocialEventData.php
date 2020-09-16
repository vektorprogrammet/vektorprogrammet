<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\SocialEvent;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSocialEventData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $socialEvent1 = new SocialEvent();
        $socialEvent1->setStartTime(DateTime::createFromFormat('Y-m-d H:i:s', '2015-08-28 17:15:00'));
        $socialEvent1->setEndTime(DateTime::createFromFormat('Y-m-d H:i:s', '2015-08-28 18:00:00'));
        $socialEvent1->setTitle("Kickoff");
        $socialEvent1->setDescription("Assistent Kickoff i Trondheim");
        $socialEvent1->setDepartment($this->getReference('dep-1'));
        $socialEvent1->setSemester($this->getReference('semester-3'));
        $manager->persist($socialEvent1);

        $socialEvent2 = new SocialEvent();
        $socialEvent2->setStartTime(new DateTime('+15 days'));
        $socialEvent2->setEndTime(new DateTime('+15 days 1 hour'));
        $socialEvent2->setTitle("Close but yet so far");
        $socialEvent2->setDescription("This is just outside 2 weeks");
        $socialEvent2->setDepartment($this->getReference('dep-1'));
        $socialEvent2->setSemester($this->getReference('semester-current'));
        $manager->persist($socialEvent2);

        $socialEvent3 = new SocialEvent();
        $socialEvent3->setStartTime((new DateTime())->modify('+2 day'));
        $socialEvent3->setEndTime((new DateTime())->modify('+2 day 1 hour'));
        $socialEvent3->setTitle("Happening soon");
        $socialEvent3->setDescription("This event is happening very soon");
        $socialEvent3->setDepartment($this->getReference('dep-1'));
        $socialEvent3->setSemester($this->getReference('semester-current'));
        $manager->persist($socialEvent3);

        $socialEvent4 = new SocialEvent();
        $socialEvent4->setStartTime((new DateTime())->modify('-24 hour'));
        $socialEvent4->setEndTime((new DateTime())->modify('-23 hour'));
        $socialEvent4->setTitle("Recent event");
        $socialEvent4->setDescription("This event has already happened");
        $socialEvent4->setDepartment($this->getReference('dep-1'));
        $socialEvent4->setSemester($this->getReference('semester-current'));
        $manager->persist($socialEvent4);


        $socialEvent5 = new SocialEvent();
        $socialEvent5->setStartTime((new DateTime())->modify('+5 hour'));
        $socialEvent5->setEndTime((new DateTime())->modify('+6 hour'));
        $socialEvent5->setTitle("eventToBeDeleted");
        $socialEvent5->setDescription("This event is deleted in tests");
        $socialEvent5->setDepartment($this->getReference('dep-1'));
        $socialEvent5->setSemester($this->getReference('semester-current'));
        $manager->persist($socialEvent5);


        $socialEvent6 = new SocialEvent();
        $socialEvent6->setStartTime(new DateTime('+13 days'));
        $socialEvent6->setEndTime(new DateTime('+13 days 1 hour'));
        $socialEvent6->setTitle("Close but not too close");
        $socialEvent6->setDescription("This is just within 2 weeks");
        $socialEvent6->setDepartment($this->getReference('dep-1'));
        $socialEvent6->setSemester($this->getReference('semester-current'));
        $manager->persist($socialEvent6);

        $manager->flush();

        $this->setReference('social-event-1', $socialEvent1);
        $this->setReference('social-event-2', $socialEvent2);
        $this->setReference('social-event-happening-soon', $socialEvent3);
        $this->setReference('recent-event', $socialEvent4);
        $this->setReference('social-event-to-be-deleted', $socialEvent5);
        $this->setReference('social-event-6', $socialEvent6);
    }

    public function getOrder()
    {
        return 5;
    }
}
