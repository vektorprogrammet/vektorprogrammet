<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewScore;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Application;

class LoadApplicationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $application0 = new Application();
        $application0->setUser($this->getReference('user-15'));
        $application0->setPreviousParticipation(false);
        $application0->setYearOfStudy(1);
        $application0->setSemester($this->getReference('semester-current'));
        $application0->setMonday('Ikke');
        $application0->setTuesday('Ikke');
        $application0->setWednesday('Ikke');
        $application0->setThursday('Ikke');
        $application0->setFriday('Bra');

        $manager->persist($application0);

        $application1 = new Application();
        $application1->setUser($this->getReference('user-10'));
        $application1->setPreviousParticipation(true);
        $application1->setYearOfStudy(1);
        $application1->setSemester($this->getReference('semester-5'));
        $application1->setMonday('Bra');
        $application1->setTuesday('Ikke');
        $application1->setWednesday('Bra');
        $application1->setThursday('Ikke');
        $application1->setFriday('Bra');

        $manager->persist($application1);

        $application2 = new Application();
        $application2->setUser($this->getReference('user-11'));
        $application2->setPreviousParticipation(false);
        $application2->setYearOfStudy(1);
        $application2->setSemester($this->getReference('semester-1'));
        $application2->setMonday('Bra');
        $application2->setTuesday('Bra');
        $application2->setWednesday('Ikke');
        $application2->setThursday('Ikke');
        $application2->setFriday('Bra');

        $manager->persist($application2);

        $application3 = new Application();
        $application3->setUser($this->getReference('user-12'));
        $application3->setPreviousParticipation(false);
        $application3->setYearOfStudy(1);
        $application3->setSemester($this->getReference('semester-current'));

        $manager->persist($application3);

        // The interview
        $interview3 = new Interview();
        $interview3->setInterviewed(true);
        $interview3->setInterviewer($this->getReference('user-2'));
        $interview3->setInterviewSchema($this->getReference('ischema-1'));
        $interview3->setUser($this->getReference('user-12'));
        $interview3->setResponseCode('code');
        $interview3->setScheduled(new \DateTime('+2 days'));
        $application3->setInterview($interview3);

        // Create answer objects for all the questions in the schema
        foreach ($interview3->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $answer = new InterviewAnswer();
            $answer->setAnswer('Test answer');
            $answer->setInterview($interview3);
            $answer->setInterviewQuestion($interviewQuestion);
            $interview3->addInterviewAnswer($answer);
        }

        // The interview score
        $intScore = new InterviewScore();
        $intScore->setSuitability(6);
        $intScore->setExplanatoryPower(5);
        $intScore->setRoleModel(4);
        $intScore->setSuitableAssistant('Ja');
        $interview3->setInterviewScore($intScore);

        // The interview practical
        $application3->setMonday('Bra');
        $application3->setTuesday('Bra');
        $application3->setWednesday('Ikke');
        $application3->setThursday('Bra');
        $application3->setFriday('Ikke');
        $application3->setHeardAboutFrom(array('Stand'));
        $application3->setEnglish(true);
        $application3->setPreferredGroup('Bolk 1');
        $application3->setDoublePosition(true);

        $manager->persist($application3);

        // This application has a conducted interview which takes some code to set up
        $application4 = new Application();
        $application4->setUser($this->getReference('user-13'));
        $application4->setPreviousParticipation(false);
        $application4->setYearOfStudy(1);
        $application4->setSemester($this->getReference('semester-current'));

        // The interview
        $interview4 = new Interview();
        $interview4->setInterviewed(true);
        $interview4->setInterviewer($this->getReference('user-2'));
        $interview4->setInterviewSchema($this->getReference('ischema-1'));
        $interview4->setUser($this->getReference('user-13'));
        $application4->setInterview($interview4);

        // Create answer objects for all the questions in the schema
        foreach ($interview4->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $answer = new InterviewAnswer();
            $answer->setAnswer('Test answer');
            $answer->setInterview($interview4);
            $answer->setInterviewQuestion($interviewQuestion);
            $interview4->addInterviewAnswer($answer);
        }

        // The interview score
        $intScore = new InterviewScore();
        $intScore->setSuitability(6);
        $intScore->setExplanatoryPower(5);
        $intScore->setRoleModel(4);
        $intScore->setSuitableAssistant('Ja');
        $interview4->setInterviewScore($intScore);

        // The interview practical
        $application4->setMonday('Bra');
        $application4->setTuesday('Bra');
        $application4->setWednesday('Ikke');
        $application4->setThursday('Bra');
        $application4->setFriday('Ikke');
        $application4->setHeardAboutFrom(array('Stand'));
        $application4->setEnglish(false);
        $application4->setPreferredGroup('Bolk 1');
        $application4->setDoublePosition(false);

        $manager->persist($application4);

        $application5 = new Application();
        $application5->setUser($this->getReference('user-14'));
        $application5->setPreviousParticipation(false);
        $application5->setYearOfStudy(1);
        $application5->setSemester($this->getReference('semester-current'));
        $interview5 = new Interview();
        $interview5->setInterviewed(false);
        $interview5->setInterviewer($this->getReference('user-2'));
        $interview5->setInterviewSchema($this->getReference('ischema-1'));
        $interview5->setUser($this->getReference('user-14'));
        $application5->setInterview($interview5);

        $manager->persist($application5);

        $application6 = new Application();
        $application6->setUser($this->getReference('user-8'));
        $application6->setPreviousParticipation(false);
        $application6->setYearOfStudy(1);
        $application6->setSemester($this->getReference('semester-current'));
        $interview6 = new Interview();
        $interview6->setInterviewed(false);
        $interview6->setInterviewer($this->getReference('user-1'));
        $interview6->setInterviewSchema($this->getReference('ischema-1'));
        $interview6->setUser($this->getReference('user-8'));
        $interview6->setCancelled(true);
        $application6->setInterview($interview6);

        $manager->persist($application6);

        $this->setReference('application-0', $application0);
        $this->setReference('application-1', $application1);
        $this->setReference('application-2', $application2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
