<?php


namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\User;
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
        $surveyTaken->setTime(new \DateTime());

        return $surveyTaken;
    }

    public function initializeTeamSurveyTaken(Survey $survey, User $user){
        $surveyTaken = $this->initializeSurveyTaken($survey);
        $surveyTaken->setUser($user);
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


    public function getValidSurveysTaken(Survey $survey) : array
    {
        $surveysTaken = $this->em->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey);
        $validSurveysTaken = array();
        foreach ($surveysTaken as $surveyTaken) {
            if ($surveyTaken === null) {
                continue;
            }
            $validSurveysTaken[] = $surveyTaken;
        }

        return $validSurveysTaken;
    }


    public function getUserAffiliationOfSurveyAnswers(Survey $survey)
    {
        $surveysTaken = $this->em->getRepository('AppBundle:SurveyTaken')->findAllTakenBySurvey($survey);
        $userAffiliation = array();
        $semester = $survey->getSemester();
        if ($survey->isTeamSurvey()) {
            foreach ($surveysTaken as $surveyTaken) {
                $user = $surveyTaken->getUser();
                $userAffiliation = $this->getUserAffiliationOfUserBySemester($user, $semester, $userAffiliation);
            }
        } else {
            foreach ($surveysTaken as $surveyTaken) {
                if (is_null($surveyTaken->getSchool())) {
                    continue;
                }
                if (!in_array($surveyTaken->getSchool()->getName(), $userAffiliation)) {
                    $userAffiliation[] = $surveyTaken->getSchool()->getName();
                }
            }
        }

        return $userAffiliation;
    }



    public function getSurveyTargetMainAffiliation(Survey $survey) : string
    {
        if ($survey->isTeamSurvey()) {
            return "Team";
        }
        return "Skole";
    }


    public function getTextAnswerWithSchoolResults($survey): array
    {
        $textQuestionArray = array();
        $textQAarray = array();

        // Get all text questions
        foreach ($survey->getSurveyQuestions() as $question) {
            if ($question->getType() == 'text') {
                $textQuestionArray[] = $question;
            }
        }

        //Collect text answers
        foreach ($textQuestionArray as $textQuestion) {
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = array();
            foreach ($textQuestion->getAnswers() as $answer) {
                if ($answer->getSurveyTaken() === null || $answer->getSurveyTaken()->getSchool() === null) {
                    continue;
                }
                $textQAarray[$questionText][] = array(
                    'answerText' => $answer->getAnswer(),
                    'schoolName' => $answer->getSurveyTaken()->getSchool()->getName()
                );
            }
        }

        return $textQAarray;
    }


    public function getTextAnswerWithTeamResults($survey): array
    {
        $textQuestionArray = array();
        $textQAarray = array();

        $semester = $survey->getSemester();

        // Get all text questions
        foreach ($survey->getSurveyQuestions() as $question) {
            if ($question->getType() == 'text') {
                $textQuestionArray[] = $question;
            }
        }

        //Collect text answers
        foreach ($textQuestionArray as $textQuestion) {
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = array();
            foreach ($textQuestion->getAnswers() as $answer) {
                if ($answer->getSurveyTaken() === null || empty($answer->getSurveyTaken()->getUser()->getTeamMemberships())) {
                    continue;
                }

                $user = $answer->getSurveyTaken()->getUser();
                $ua = $this->getUserAffiliationOfUserBySemester($user, $semester);
                $teamNames = $this->getTeamNamesAsString($ua);


                $textQAarray[$questionText][] = array(
                    'answerText' => $answer->getAnswer(),
                    'teamName' => $teamNames,
                );
            }
        }

        return $textQAarray;
    }


    private function getTeamNamesAsString(array $teamNames): string
    {
        $teamNames = implode(", ", $teamNames);
        $find = ',';
        $replace = ' og';
        $teamNames = strrev(preg_replace(strrev("/$find/"), strrev($replace), strrev($teamNames), 1));
        return $teamNames;
    }


    private function getUserAffiliationOfUserBySemester(User $user, Semester $semester, $userAffiliation = array()) : array
    {
        $teamMemberships = $this->em->getRepository('AppBundle:TeamMembership')->findTeamMembershipsByUserAndSemester($user, $semester);

        foreach ($teamMemberships as $teamMembership) {
            $teamName = $teamMembership->getTeam()->getName();
            if (!in_array($teamName, $userAffiliation)) {
                $userAffiliation[] = $teamName;
            }
        }

        if (empty($teamMemberships)) {
            $userAffiliation[]= "Ikke teammedlem";
        }

        return $userAffiliation;
    }

    public function surveyResultToJson(Survey $survey)
    {
        $userAffiliation = $this->getUserAffiliationOfSurveyAnswers($survey);
        $validSurveysTaken = $this->getValidSurveysTaken($survey);
        $title = $this->getSurveyTargetMainAffiliation($survey);

        //Inject the school/team question into question array
        $userAffiliationQuestion = array('question_id' => 0, 'question_label' => $title, 'alternatives' => $userAffiliation);
        $survey_json = json_encode($survey);
        $survey_decode = json_decode($survey_json, true);
        $survey_decode['questions'][] = $userAffiliationQuestion;

        return array('survey' => $survey_decode, 'answers' => $validSurveysTaken);
    }

    public function toggleReservePopUp(User $user)
    {
        $user->setReservedPopUp(!$user->getReservedPopUp());
        $user->setLastPopUp(null);

        try{
            $this->em->persist($user);
            $this->em->flush();
        }catch (\Exception $e){

        }

    }
        public function closePopUp(User $user){
        $user->setLastPopUp(new \DateTime());
        try{
            $this->em->persist($user);
            $this->em->flush();
        }catch (\Exception $e){

        }
    }

}
