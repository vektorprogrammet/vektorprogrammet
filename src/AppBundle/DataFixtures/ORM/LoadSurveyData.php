<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Position;
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
        $surveyQuestion1->setOptional(false);
        $surveyQuestion1->setType('text');


        $surveyQuestion2->setQuestion('Ja eller nei?');
        $surveyQuestion2->setHelp('Svar ja eller nei');
        $surveyQuestion2->setOptional(false);
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


        $teamSurvey1 = new Survey();
        $schoolSurvey1 = new Survey();

        $semester = $this->getReference('semester-4');

        $teamSurvey1->setSemester($semester);
        $teamSurvey1->setShowCustomFinishPage(true);
        $teamSurvey1->setFinishPageContent("Takk for at du svarte på vår undersøkelse");
        $teamSurvey1->setName("Team Survey");
        $teamSurvey1->setSurveyPopUpMessage("Svar på undersøkelse!");
        $teamSurvey1->setConfidential(false);
        $teamSurvey1->setTeamSurvey(true);
        $teamSurvey1->addSurveyQuestion($surveyQuestion1);
        $teamSurvey1->addSurveyQuestion($surveyQuestion2);

        $schoolSurvey1->setSemester($semester);
        $schoolSurvey1->setShowCustomFinishPage(false);
        $schoolSurvey1->setName("School Survey");
        $schoolSurvey1->setConfidential(false);
        $schoolSurvey1->setTeamSurvey(false);
        $schoolSurvey1->addSurveyQuestion($surveyQuestion3);


        $this->addReference('teamSurvey1', $teamSurvey1);
        $this->addReference('$schoolSurvey1', $schoolSurvey1);

        $manager->persist($teamSurvey1);
        $manager->persist($schoolSurvey1);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
