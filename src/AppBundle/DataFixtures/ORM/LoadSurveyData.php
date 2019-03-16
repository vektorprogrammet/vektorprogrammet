<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyQuestion;
use AppBundle\Entity\SurveyQuestionAlternative;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSurveyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $surveyQuestion1 = new SurveyQuestion();
        $surveyQuestion2 = new SurveyQuestion();

        $surveyQuestion1->setQuestion('Hva heter du?');
        $surveyQuestion1->setHelp('Oppgi navn');
        $surveyQuestion1->setOptional(true);
        $surveyQuestion1->setType('text');


        $surveyQuestion2->setQuestion('Ja eller nei?');
        $surveyQuestion2->setHelp('Svar ja eller nei');
        $surveyQuestion2->setOptional(true);
        $surveyQuestion2->setType('check');

        $surveyQuestionAlternative1 = new SurveyQuestionAlternative();
        $surveyQuestionAlternative1->setSurveyQuestion($surveyQuestion2);
        $surveyQuestionAlternative1->setAlternative("Ja");
        $surveyQuestionAlternative2 = new SurveyQuestionAlternative();
        $surveyQuestionAlternative2->setSurveyQuestion($surveyQuestion2);
        $surveyQuestionAlternative2->setAlternative("Nei");

        $surveyQuestion2->addAlternative($surveyQuestionAlternative1);
        $surveyQuestion2->addAlternative($surveyQuestionAlternative2);

        $surveyQuestion3 = clone $surveyQuestion1;

        $manager->persist($surveyQuestion1);
        $manager->persist($surveyQuestion2);
        $manager->persist($surveyQuestion3);
        $manager->flush();

        $semester = $this->getReference('semester-current');
        $department = $this->getReference('dep-1');

        $teamSurvey1 = new Survey();
        $teamSurvey1->setSemester($semester);
        $teamSurvey1->setDepartment($department);
        $teamSurvey1->setFinishPageContent("Takk for at du svarte på vår undersøkelse");
        $teamSurvey1->setName("Team Survey");
        $teamSurvey1->setSurveyPopUpMessage("Svar på undersøkelse!");
        $teamSurvey1->setConfidential(false);
        $teamSurvey1->setTargetAudience(Survey::$TEAM_SURVEY);
        $teamSurvey1->addSurveyQuestion($surveyQuestion1);
        $teamSurvey1->addSurveyQuestion($surveyQuestion2);
        $teamSurvey1->setShowCustomPopUpMessage(true);



        $teamSurvey2 = new Survey();
        $teamSurvey2->setSemester($semester);
        $teamSurvey2->setDepartment($department);
        $teamSurvey2->setFinishPageContent("Takk for at du svarte på vår undersøkelse");
        $teamSurvey2->setName("Team Survey");
        $teamSurvey2->setSurveyPopUpMessage("Svar på undersøkelse!");
        $teamSurvey2->setConfidential(false);
        $teamSurvey2->setTargetAudience(Survey::$TEAM_SURVEY);
        $teamSurvey2->addSurveyQuestion($surveyQuestion1);
        $teamSurvey2->addSurveyQuestion($surveyQuestion2);
        $teamSurvey2->setShowCustomPopUpMessage(true);


        $teamSurvey3 = new Survey();
        $teamSurvey3->setSemester($semester);
        $teamSurvey3->setDepartment($department);
        $teamSurvey3->setFinishPageContent("Takk for at du svarte på vår undersøkelse");
        $teamSurvey3->setName("Team Survey");
        $teamSurvey3->setSurveyPopUpMessage("Svar på undersøkelse!");
        $teamSurvey3->setConfidential(false);
        $teamSurvey3->setTargetAudience(Survey::$TEAM_SURVEY);
        $teamSurvey3->addSurveyQuestion($surveyQuestion1);
        $teamSurvey3->addSurveyQuestion($surveyQuestion2);
        $teamSurvey3->setShowCustomPopUpMessage(true);


        $teamSurvey4 = new Survey();
        $teamSurvey4->setSemester($semester);
        $teamSurvey4->setDepartment($department);
        $teamSurvey4->setFinishPageContent("Takk for at du svarte på vår undersøkelse");
        $teamSurvey4->setName("Team Survey");
        $teamSurvey4->setSurveyPopUpMessage("Svar på undersøkelse!");
        $teamSurvey4->setConfidential(false);
        $teamSurvey4->setTargetAudience(Survey::$TEAM_SURVEY);
        $teamSurvey4->addSurveyQuestion($surveyQuestion1);
        $teamSurvey4->addSurveyQuestion($surveyQuestion2);
        $teamSurvey4->setShowCustomPopUpMessage(true);

        $schoolSurvey1 = new Survey();
        $schoolSurvey1->setSemester($semester);
        $schoolSurvey1->setDepartment($department);
        $schoolSurvey1->setName("School Survey");
        $schoolSurvey1->setConfidential(false);
        $schoolSurvey1->setTargetAudience(Survey::$ASSISTANT_SURVEY);
        $schoolSurvey1->addSurveyQuestion($surveyQuestion3);


        $this->addReference('team-survey-1', $teamSurvey1);
        $this->addReference('team-survey-2', $teamSurvey2);
        $this->addReference('team-survey-3', $teamSurvey3);
        $this->addReference('team-survey-4', $teamSurvey4);
        $this->addReference('school-survey-1', $schoolSurvey1);

        $manager->persist($teamSurvey1);
        $manager->persist($teamSurvey2);
        $manager->persist($teamSurvey3);
        $manager->persist($teamSurvey4);
        $manager->persist($schoolSurvey1);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
