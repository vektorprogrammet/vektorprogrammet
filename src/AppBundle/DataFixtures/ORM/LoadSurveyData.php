<?php


namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\SurveyPupil;
use AppBundle\Entity\SurveyTeacher;


class LoadSurveyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        for($i = 0; $i < 100; $i++){
            $randSchool = rand(1, 3);
            $pSurvey = new surveyPupil();
            $pSurvey->setSchool($this->getReference('school-'.$randSchool.''));
            $pSurvey->setQuestion1(rand(1,2));
            $pSurvey->setQuestion2(rand(1,6));
            $pSurvey->setQuestion3(rand(1,2));
            $pSurvey->setQuestion4(rand(1,2));
            $pSurvey->setQuestion5(rand(1,5));
            $pSurvey->setQuestion6(rand(1,5));
            $pSurvey->setQuestion7(rand(1,5));
            $pSurvey->setQuestion8(rand(1,5));
            $pSurvey->setQuestion9(rand(1,5));
            $manager->persist($pSurvey);
        }

        for($i = 0; $i < 100; $i++){
            $randSchool = rand(1, 3);
            $tSurvey = new surveyTeacher();
            $tSurvey->setSchool($this->getReference('school-'.$randSchool.''));
            $tSurvey->setQuestion1(rand(1,3));
            $tSurvey->setQuestion2(rand(1,5));
            $tSurvey->setQuestion3(rand(1,5));
            $tSurvey->setQuestion4(rand(1,5));
            $tSurvey->setQuestion5(rand(1,5));
            $tSurvey->setQuestion6(rand(1,5));
            $tSurvey->setQuestion7(rand(1,5));
            $tSurvey->setQuestion8(rand(1,5));
            $tSurvey->setQuestion9(rand(1,5));
            $manager->persist($tSurvey);
        }


        $manager->flush();


    }

    public function getOrder()
    {
        return 2;
    }
}