<?php
/**
 * Created by IntelliJ IDEA.
 * User: kristoffer
 * Date: 19.12.16
 * Time: 21:59.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveyTaken;
use Doctrine\ORM\EntityManager;

class SurveyManager
{
    private $em;

    /**
     * SurveyManager constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function initializeSurveyTaken(Survey $survey): SurveyTaken
    {
        $surveyTaken = new SurveyTaken();
        $surveyTaken->setSurvey($survey);
        foreach ($survey->getSurveyQuestions() as $surveyQuestion) {
            $answer = new SurveyAnswer();
            $answer->setSurveyQuestion($surveyQuestion);
            $answer->setSurveyTaken($surveyTaken);

            $surveyTaken->addSurveyAnswer($answer);
        }

        return $surveyTaken;
    }

    public function predictSurveyTakenAnswers(SurveyTaken $surveyTaken): SurveyTaken
    {
        $allTakenSurveys = $this->em->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($surveyTaken->getSurvey());

        if (count($allTakenSurveys) === 0) {
            return $surveyTaken;
        }

        $countAnswer = array();
        foreach ($allTakenSurveys as $takenSurvey) {
            foreach ($takenSurvey->getSurveyAnswers() as $answer) {
                if ((!($answer->getSurveyQuestion()->getType() == 'radio' || $answer->getSurveyQuestion()->getType() == 'list')) || $answer->getSurveyQuestion()->getOptional()) {
                    continue;
                }
                if (!isset($countAnswer[$answer->getSurveyQuestion()->getId()])) {
                    $countAnswer[$answer->getSurveyQuestion()->getId()] = array();
                }
                if (!isset($countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()])) {
                    $countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()] = 0;
                }
                ++$countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()];
            }
        }

        foreach ($surveyTaken->getSurveyAnswers() as $answer) {
            if ((!($answer->getSurveyQuestion()->getType() == 'radio' || $answer->getSurveyQuestion()->getType() == 'list')) || $answer->getSurveyQuestion()->getOptional()) {
                continue;
            }
            $answer->setAnswer(array_keys($countAnswer[$answer->getSurveyQuestion()->getId()], max($countAnswer[$answer->getSurveyQuestion()->getId()]))[0]);
        }

        $surveyTaken->setSchool(end($allTakenSurveys)->getSchool());

        return $surveyTaken;
    }
}
