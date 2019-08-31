<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyNotification;
use AppBundle\Entity\SurveyNotificationCollection;
use AppBundle\Entity\SurveyQuestion;
use AppBundle\Entity\SurveyQuestionAlternative;
use AppBundle\Entity\UserGroup;
use AppBundle\Entity\UserGroupCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSurveyNotificationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $survey1 = $this->getReference("school-survey-1");

        $user1 = $this->getReference("user-1");
        $user2 = $this->getReference("user-2");
        $user3 = $this->getReference("user-3");
        $user4 = $this->getReference("user-4");

        $userGroup1A = $this->getReference("usergroup1A");
        $usergroup1B = $this->getReference("usergroup1B");
        $usergroup2A = $this->getReference("usergroup2A");
        $usergroup2B = $this->getReference("usergroup2B");



        $surveyNotificationCollection1 = new SurveyNotificationCollection();
        $surveyNotificationCollection2 = new SurveyNotificationCollection();


        $surveyNotification1 = new SurveyNotification();
        $surveyNotification2 = new SurveyNotification();
        $surveyNotification3 = new SurveyNotification();
        $surveyNotification4 = new SurveyNotification();
        $surveyNotification5 = new SurveyNotification();
        $surveyNotification6 = new SurveyNotification();
        $surveyNotification7 = new SurveyNotification();
        $surveyNotification8 = new SurveyNotification();

        $surveyNotification1->setUser($user1);
        $surveyNotification2->setUser($user2);
        $surveyNotification3->setUser($user3);
        $surveyNotification4->setUser($user4);
        $surveyNotification5->setUser($user1);
        $surveyNotification6->setUser($user2);
        $surveyNotification7->setUser($user3);
        $surveyNotification8->setUser($user4);

        $surveyNotification1->setUserIdentifier("test1");
        $surveyNotification2->setUserIdentifier("test2");
        $surveyNotification3->setUserIdentifier("test3");
        $surveyNotification4->setUserIdentifier("test4");
        $surveyNotification5->setUserIdentifier("test5");
        $surveyNotification6->setUserIdentifier("test6");
        $surveyNotification7->setUserIdentifier("test7");
        $surveyNotification8->setUserIdentifier("test8");

        $surveyNotification1->setSurveyNotificationCollection($surveyNotificationCollection1);
        $surveyNotification2->setSurveyNotificationCollection($surveyNotificationCollection1);
        $surveyNotification3->setSurveyNotificationCollection($surveyNotificationCollection1);
        $surveyNotification4->setSurveyNotificationCollection($surveyNotificationCollection1);
        $surveyNotification5->setSurveyNotificationCollection($surveyNotificationCollection2);
        $surveyNotification6->setSurveyNotificationCollection($surveyNotificationCollection2);
        $surveyNotification7->setSurveyNotificationCollection($surveyNotificationCollection2);
        $surveyNotification8->setSurveyNotificationCollection($surveyNotificationCollection2);

        $surveyNotifications1 = array($surveyNotification1,$surveyNotification2, $surveyNotification3, $surveyNotification4);

        $surveyNotifications2 = array($surveyNotification5,$surveyNotification6, $surveyNotification7, $surveyNotification8);



        $surveyNotificationCollection1->setName("Notifcations Epost");
        $surveyNotificationCollection1->setSurvey($survey1);
        $surveyNotificationCollection1->setSurveyNotifications($surveyNotifications1);
        $surveyNotificationCollection1->setEmailEndMessage("Test");
        $surveyNotificationCollection1->setEmailFromName("Amir");
        $surveyNotificationCollection1->setEmailMessage("Test melding");
        $surveyNotificationCollection1->setEmailSubject("Emne");
        $surveyNotificationCollection1->setNotificationType(SurveyNotificationCollection::$EMAIL_NOTIFICATION);
        $surveyNotificationCollection1->setUserGroups(array($usergroup1B,$usergroup2B));
        $surveyNotificationCollection1->setTimeOfNotification(new \DateTime());


        $surveyNotificationCollection2->setName("Notifcations SMS");
        $surveyNotificationCollection2->setSurvey($survey1);
        $surveyNotificationCollection2->setSurveyNotifications($surveyNotifications2);
        $surveyNotificationCollection2->setSmsMessage("Dette er en test");
        $surveyNotificationCollection2->setNotificationType(SurveyNotificationCollection::$SMS_NOTIFICATION);
        $surveyNotificationCollection2->setUserGroups(array($userGroup1A,$usergroup2A));
        $surveyNotificationCollection2->setTimeOfNotification(new \DateTime());


        $this->addReference("SurveyNotificationCollection1", $surveyNotificationCollection1);
        $this->addReference("SurveyNotificationCollection2", $surveyNotificationCollection2);

        $manager->persist($surveyNotification1);
        $manager->persist($surveyNotification2);
        $manager->persist($surveyNotification3);
        $manager->persist($surveyNotification4);
        $manager->persist($surveyNotification5);
        $manager->persist($surveyNotification6);
        $manager->persist($surveyNotification7);
        $manager->persist($surveyNotification8);
        $manager->persist($surveyNotificationCollection1);
        $manager->persist($surveyNotificationCollection2);
        $manager->flush();

    }

    public function getOrder()
    {
        return 7;
    }
}
