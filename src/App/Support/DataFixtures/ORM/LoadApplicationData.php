<?php

namespace App\Support\DataFixtures\ORM;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\Interview;
use App\Entity\InterviewAnswer;
use App\Entity\InterviewSchema;
use App\Entity\InterviewScore;
use App\Entity\Team;
use App\Entity\User;
use App\Type\InterviewStatusType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadApplicationData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $application0 = new Application();
        $application0->setUser($this->getReference('user-team-member', User::class));
        $application0->setPreviousParticipation(false);
        $application0->setYearOfStudy(1);
        $application0->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));
        $application0->setMonday(false);
        $application0->setTuesday(false);
        $application0->setWednesday(false);
        $application0->setThursday(false);
        $application0->setFriday(true);
        $application0->setSubstitute(true);

        $manager->persist($application0);

        $application1 = new Application();
        $application1->setUser($this->getReference('user-10', User::class));
        $application1->setPreviousParticipation(true);
        $application1->setYearOfStudy(1);
        $application1->setAdmissionPeriod($this->getReference('admission-period-5', AdmissionPeriod::class));
        $application1->setMonday(true);
        $application1->setTuesday(false);
        $application1->setWednesday(true);
        $application1->setThursday(false);
        $application1->setFriday(true);
        $application0->setSubstitute(true);

        $manager->persist($application1);

        $application2 = new Application();
        $application2->setUser($this->getReference('user-11', User::class));
        $application2->setPreviousParticipation(false);
        $application2->setYearOfStudy(1);
        $application2->setAdmissionPeriod($this->getReference('admission-period-1', AdmissionPeriod::class));
        $application2->setMonday(true);
        $application2->setTuesday(true);
        $application2->setWednesday(false);
        $application2->setThursday(false);
        $application2->setFriday(true);
        $application0->setSubstitute(true);

        $manager->persist($application2);

        $application3 = new Application();
        $application3->setUser($this->getReference('user-12', User::class));
        $application3->setPreviousParticipation(false);
        $application3->setYearOfStudy(1);
        $application3->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));

        $manager->persist($application3);

        // The interview
        $interview3 = new Interview();
        $interview3->setInterviewed(true);
        $interview3->setInterviewer($this->getReference('user-2', User::class));
        $interview3->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview3->setUser($this->getReference('user-12', User::class));
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
        $application3->setMonday(true);
        $application3->setTuesday(true);
        $application3->setWednesday(false);
        $application3->setThursday(true);
        $application3->setFriday(false);
        $application3->setHeardAboutFrom(['Stand']);
        $application3->setLanguage('Norsk og engelsk');
        $application3->setPreferredGroup('Bolk 1');
        $application3->setDoublePosition(true);
        $application3->setTeamInterest(true);
        $application3->setPotentialTeams([
            $this->getReference('team-1', Team::class),
            $this->getReference('team-2', Team::class),
        ]);

        $manager->persist($application3);

        // This application has a conducted interview which takes some code to set up
        $application4 = new Application();
        $application4->setUser($this->getReference('user-13', User::class));
        $application4->setPreviousParticipation(false);
        $application4->setYearOfStudy(1);
        $application4->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));

        // The interview
        $interview4 = new Interview();
        $interview4->setInterviewed(true);
        $interview4->setInterviewer($this->getReference('user-2', User::class));
        $interview4->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview4->setUser($this->getReference('user-13', User::class));
        $application4->setTeamInterest(true);
        $application4->setPotentialTeams([$this->getReference('team-1', Team::class)]);
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
        $application4->setMonday(true);
        $application4->setTuesday(true);
        $application4->setWednesday(false);
        $application4->setThursday(true);
        $application4->setFriday(false);
        $application4->setHeardAboutFrom(['Stand']);
        $application4->setLanguage('Norsk');
        $application4->setPreferredGroup('Bolk 1');
        $application4->setDoublePosition(false);

        $manager->persist($application4);

        $application5 = new Application();
        $application5->setUser($this->getReference('user-assistant', User::class));
        $application5->setPreviousParticipation(false);
        $application5->setYearOfStudy(1);
        $application5->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));
        $interview5 = new Interview();
        $interview5->setInterviewed(false);
        $interview5->setInterviewer($this->getReference('user-2', User::class));
        $interview5->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview5->setUser($this->getReference('user-assistant', User::class));
        $interview5->setResponseCode('code');
        $interview5->setScheduled(new \DateTime('+2 days'));
        $interview5->setInterviewStatus(InterviewStatusType::PENDING);
        $interview5->setRoom('D1-123');
        $interview5->setCampus('Gløshaugen');
        $application5->setInterview($interview5);

        $manager->persist($application5);

        $application6 = new Application();
        $application6->setUser($this->getReference('user-8', User::class));
        $application6->setPreviousParticipation(false);
        $application6->setYearOfStudy(1);
        $application6->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));
        $interview6 = new Interview();
        $interview6->setInterviewed(false);
        $interview6->setInterviewer($this->getReference('user-1', User::class));
        $interview6->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview6->setUser($this->getReference('user-8', User::class));
        $interview6->setCancelled(true);
        $interview6->setCancelMessage('
        Jeg er en kjiping som ikke orker å være vektorassistent.
        Jeg har haugevis av unnskyldninger på lager for å slippe unna disse greine.
        Helst ikke kontakt meg igjen. Ever.
        Jeg har haugevis av unnskyldninger på lager for å slippe unna disse greine.
        Jeg er en kjiping som ikke orker å være vektorassistent.
        Helst ikke kontakt meg igjen. Ever.
        Jeg er en kjiping som ikke orker å være vektorassistent.');
        $application6->setInterview($interview6);

        $manager->persist($application6);

        /* Person 20: Jan-Per-Gustavio */
        $this->setReference('application-0', $application0);
        $application20 = new Application();
        $this->setReference('application-1', $application1);
        $application20->setUser($this->getReference('user-20', User::class));
        $this->setReference('application-2', $application2);
        $application20->setPreviousParticipation(false);
        $application20->setYearOfStudy(1);
        $application20->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));

        $application20->setMonday(false);
        $application20->setTuesday(false);
        $application20->setWednesday(false);
        $application20->setThursday(false);
        $application20->setFriday(true);
        $application20->setHeardAboutFrom(['Stand']);
        $application20->setLanguage('Norsk');
        $application20->setPreferredGroup('Bolk 1');
        $application20->setDoublePosition(true);

        $interview20 = new Interview();
        $interview20->setInterviewed(true);
        $interview20->setInterviewer($this->getReference('user-2', User::class));
        $interview20->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview20->setUser($this->getReference('user-20', User::class));
        $interview20->setCancelled(false);

        // Create answer objects for all the questions in the schema
        foreach ($interview20->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $answer = new InterviewAnswer();
            $answer->setAnswer('Test answer');
            $answer->setInterview($interview20);
            $answer->setInterviewQuestion($interviewQuestion);
            $interview20->addInterviewAnswer($answer);
        }

        // The interview score
        $intScore = new InterviewScore();
        $intScore->setSuitability(6);
        $intScore->setExplanatoryPower(5);
        $intScore->setRoleModel(4);
        $intScore->setSuitableAssistant('Ja');
        $interview20->setInterviewScore($intScore);
        $application20->setInterview($interview20);

        $manager->persist($application20);

        $application21 = new Application();
        $application21->setUser($this->getReference('user-14', User::class));
        $application21->setPreviousParticipation(false);
        $application21->setYearOfStudy(1);
        $application21->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));
        $application21->setMonday('Ikke');
        $application21->setTuesday('Ikke');
        $application21->setWednesday('Ikke');
        $application21->setThursday('Ikke');
        $application21->setFriday('Bra');
        $application21->setSubstitute(true);
        $application21->setPreferredGroup('Bolk 1');
        $interview21 = new Interview();
        $interview21->setInterviewed(true);
        $interview21->setInterviewer($this->getReference('user-2', User::class));
        $interview21->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview21->setUser($this->getReference('user-14', User::class));
        $interview21->setCancelled(false);
        $application21->setInterview($interview21);

        $intScore = new InterviewScore();
        $intScore->setSuitability(6);
        $intScore->setExplanatoryPower(5);
        $intScore->setRoleModel(4);
        $intScore->setSuitableAssistant('Ja');
        $interview21->setInterviewScore($intScore);
        $application21->setInterview($interview21);

        $manager->persist($application21);
        $manager->persist($interview21);

        for ($i = 0; $i < 100; ++$i) {
            $user = $this->getReference('scheduling-user-'.$i, User::class);
            $this->createSchedulingApplication($user, $manager);
        }

        $this->setReference('application-0', $application0);
        $this->setReference('application-1', $application1);
        $this->setReference('application-2', $application2);

        $this->getReference('team-1', Team::class)->setPotentialMembers([$application3, $application4]);
        $this->getReference('team-2', Team::class)->setPotentialMembers([$application3]);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 5;
    }

    private function createSchedulingApplication(User $user, $manager)
    {
        $application = new Application();
        $application->setUser($user);
        $application->setPreviousParticipation(mt_rand(0, 100) < 10 ? true : false);
        $application->setYearOfStudy(1);
        $application->setAdmissionPeriod($this->getReference('admission-period-current', AdmissionPeriod::class));
        $application->setCreated(new \DateTime('-'.mt_rand(0, 10).'days'));
        $randomArr = [true, false, false, false, false];
        shuffle($randomArr);
        $application->setMonday($randomArr[0] || mt_rand(0, 100) < 20);
        $application->setTuesday($randomArr[1] || mt_rand(0, 100) < 20);
        $application->setWednesday($randomArr[2] || mt_rand(0, 100) < 20);
        $application->setThursday($randomArr[3] || mt_rand(0, 100) < 20);
        $application->setFriday($randomArr[4] || mt_rand(0, 100) < 20);
        $application->setHeardAboutFrom(['Stand']);
        $application->setLanguage($randomArr[0] || mt_rand(0, 100) < 20 ? 'Norsk' : 'Engelsk');
        $application->setPreferredGroup(mt_rand(0, 100) < 50 ? 'Bolk 1' : 'Bolk 2');
        $application->setDoublePosition(mt_rand(0, 100) < 10 ? true : false);

        $interview = new Interview();
        $interview->setInterviewed(true);
        $interview->setInterviewer($this->getReference('user-2', User::class));
        $interview->setInterviewSchema($this->getReference('ischema-1', InterviewSchema::class));
        $interview->setUser($user);
        $interview->setCancelled(false);

        // Create answer objects for all the questions in the schema
        foreach ($interview->getInterviewSchema()->getInterviewQuestions() as $interviewQuestion) {
            $answer = new InterviewAnswer();
            $answer->setAnswer('Test answer');
            $answer->setInterview($interview);
            $answer->setInterviewQuestion($interviewQuestion);
            $interview->addInterviewAnswer($answer);
        }

        // The interview score
        $intScore = new InterviewScore();
        $intScore->setSuitability(mt_rand(4, 6));
        $intScore->setExplanatoryPower(mt_rand(4, 6));
        $intScore->setRoleModel(mt_rand(4, 6));
        $intScore->setSuitableAssistant('Ja');
        $interview->setInterviewScore($intScore);
        $application->setInterview($interview);

        $manager->persist($application);
    }
}
