<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewPractical;
use AppBundle\Entity\InterviewScore;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Application;
use AppBundle\Entity\ApplicationStatistic;

class LoadApplicationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $application1 = new Application();
        $application1->setFirstName('Marius');
        $application1->setLastName('Svendsen');
        $application1->setEmail('marius@gmail.com');
        $application1->setPhone('95321485');
        $application1->setUserCreated(false);
        $application1->setSubstituteCreated(false);
        $as1 = new ApplicationStatistic();
        $as1->setGender(0);
        $as1->setPreviousParticipation(true);
        $as1->setAccepted(0);
        $as1->setYearOfStudy(1);
        $as1->setFieldOfStudy($this->getReference('fos-1'));
        $as1->setSemester($this->getReference('semester-1'));
        $application1->setStatistic($as1);

        $manager->persist($application1);

        $application2 = new Application();
        $application2->setFirstName('Siri');
        $application2->setLastName('Kristiansen');
        $application2->setEmail('siri@kristiansen.no');
        $application2->setPhone('95254873');
        $application2->setUserCreated(false);
        $application2->setSubstituteCreated(false);
        $as2 = new ApplicationStatistic();
        $as2->setGender(0);
        $as2->setPreviousParticipation(true);
        $as2->setAccepted(0);
        $as2->setYearOfStudy(1);
        $as2->setFieldOfStudy($this->getReference('fos-2'));
        $as2->setSemester($this->getReference('semester-1'));
        $application2->setStatistic($as2);

        $manager->persist($application2);

        $application3 = new Application();
        $application3->setFirstName('Leonardo');
        $application3->setLastName('DiCaprio');
        $application3->setEmail('leonardo@hollywood.com');
        $application3->setPhone('95235816');
        $application3->setUserCreated(false);
        $application3->setSubstituteCreated(false);
        $as3 = new ApplicationStatistic();
        $as3->setGender(0);
        $as3->setPreviousParticipation(true);
        $as3->setAccepted(0);
        $as3->setYearOfStudy(1);
        $as3->setFieldOfStudy($this->getReference('fos-2'));
        $as3->setSemester($this->getReference('semester-1'));
        $application3->setStatistic($as3);

        $manager->persist($application3);

        // This application has a conducted interview which takes some code to set up
        $application4 = new Application();
        $application4->setFirstName('Walter');
        $application4->setLastName('White');
        $application4->setEmail('walter@white.com');
        $application4->setPhone('95254873');
        $application4->setUserCreated(false);
        $application4->setSubstituteCreated(false);
        $as4 = new ApplicationStatistic();
        $as4->setGender(0);
        $as4->setPreviousParticipation(true);
        $as4->setAccepted(0);
        $as4->setYearOfStudy(1);
        $as4->setFieldOfStudy($this->getReference('fos-1'));
        $as4->setSemester($this->getReference('semester-1'));
        $application4->setStatistic($as4);

        // The interview
        $interview4 = new Interview();
        $interview4->setInterviewed(true);
        $interview4->setInterviewer($this->getReference('user-2'));
        $interview4->setInterviewSchema($this->getReference('ischema-1'));
        $interview4->setApplication($application4);
        $application4->setInterview($interview4);

        // Create answer objects for all the questions in the schema
        foreach($interview4->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $answer = new InterviewAnswer();
            $answer->setAnswer("Test answer");
            $answer->setInterview($interview4);
            $answer->setInterviewQuestion($interviewQuestion);
            $interview4->addInterviewAnswer($answer);
        }

        // The interview score
        $intScore = new InterviewScore();
        $intScore->setSuitability(3);
        $intScore->setExplanatoryPower(3);
        $intScore->setRoleModel(3);
        $intScore->setSuitableAssistant('Ja');
        $intScore->setApplicationStatistic($as4);
        $interview4->setInterviewScore($intScore);

        // The interview practical
        $intPrac = new InterviewPractical();
        $intPrac->setMonday("Bra");
        $intPrac->setTuesday("Bra");
        $intPrac->setWednesday("Bra");
        $intPrac->setThursday("Bra");
        $intPrac->setFriday("Bra");
        $intPrac->setComment("Test");
        $intPrac->setHeardAboutFrom("Stand");
        $intPrac->setEnglish(true);
        $intPrac->setPosition("2x2");
        $intPrac->setSubstitute(true);
        $intPrac->setApplicationStatistic($as4);
        $interview4->setInterviewPractical($intPrac);

        $manager->persist($application4);

        $application5 = new Application();
        $application5->setFirstName('Mark');
        $application5->setLastName('Zuckerberg');
        $application5->setEmail('mark@facebook.com');
        $application5->setPhone('95856472');
        $application5->setUserCreated(false);
        $application5->setSubstituteCreated(false);
        $as5 = new ApplicationStatistic();
        $as5->setGender(0);
        $as5->setPreviousParticipation(true);
        $as5->setAccepted(0);
        $as5->setYearOfStudy(1);
        $as5->setFieldOfStudy($this->getReference('fos-1'));
        $as5->setSemester($this->getReference('semester-1'));
        $application5->setStatistic($as5);
        $interview5 = new Interview();
        $interview5->setInterviewed(false);
        $interview5->setInterviewer($this->getReference('user-2'));
        $interview5->setInterviewSchema($this->getReference('ischema-1'));
        $application5->setInterview($interview5);


        $manager->persist($application5);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}