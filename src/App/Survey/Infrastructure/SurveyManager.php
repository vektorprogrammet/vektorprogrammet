<?php

namespace App\Survey\Infrastructure;

use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Shared\Entity\Semester;
use App\Support\Utils\CsvUtil;
use App\Survey\Domain\Rules\SurveyDataTransformer;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyAnswer;
use App\Survey\Infrastructure\Entity\SurveyTaken;
use Doctrine\ORM\EntityManagerInterface;

class SurveyManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyDataTransformer $dataTransformer,
    ) {
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

    public function initializeUserSurveyTaken(Survey $survey, User $user)
    {
        $surveyTaken = $this->initializeSurveyTaken($survey);
        $surveyTaken->setUser($user);

        return $surveyTaken;
    }

    public function predictSurveyTakenAnswers(SurveyTaken $surveyTaken): SurveyTaken
    {
        $allTakenSurveys = $this->em->getRepository(SurveyTaken::class)->findAllTakenBySurvey($surveyTaken->getSurvey());

        if (count($allTakenSurveys) === 0) {
            return $surveyTaken;
        }

        $countAnswer = [];
        foreach ($allTakenSurveys as $takenSurvey) {
            foreach ($takenSurvey->getSurveyAnswers() as $answer) {
                if ((!($answer->getSurveyQuestion()->getType() === 'radio' || $answer->getSurveyQuestion()->getType() === 'list')) || $answer->getSurveyQuestion()->getOptional()) {
                    continue;
                }
                if (!isset($countAnswer[$answer->getSurveyQuestion()->getId()])) {
                    $countAnswer[$answer->getSurveyQuestion()->getId()] = [];
                }
                if (!isset($countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()])) {
                    $countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()] = 0;
                }
                ++$countAnswer[$answer->getSurveyQuestion()->getId()][$answer->getAnswer()];
            }
        }

        foreach ($surveyTaken->getSurveyAnswers() as $answer) {
            if ((!($answer->getSurveyQuestion()->getType() === 'radio' || $answer->getSurveyQuestion()->getType() === 'list')) || $answer->getSurveyQuestion()->getOptional()) {
                continue;
            }
            $answer->setAnswer(array_keys($countAnswer[$answer->getSurveyQuestion()->getId()], max($countAnswer[$answer->getSurveyQuestion()->getId()]), true)[0]);
        }

        $surveyTaken->setSchool(end($allTakenSurveys)->getSchool());

        return $surveyTaken;
    }

    public function getUserAffiliationOfSurveyAnswers(Survey $survey)
    {
        $surveysTaken = $this->em->getRepository(SurveyTaken::class)->findAllTakenBySurvey($survey);
        $userAffiliation = [];
        $semester = $survey->getSemester();
        if ($survey->getTargetAudience() === Survey::$TEAM_SURVEY) {
            foreach ($surveysTaken as $surveyTaken) {
                $user = $surveyTaken->getUser();
                $userAffiliation = $this->getUserAffiliationOfUserBySemester($user, $semester, $userAffiliation);
            }
        } else {
            foreach ($surveysTaken as $surveyTaken) {
                if ($surveyTaken->getSchool() === null) {
                    continue;
                }
                if (!in_array($surveyTaken->getSchool()->getName(), $userAffiliation, true)) {
                    $userAffiliation[] = $surveyTaken->getSchool()->getName();
                }
            }
        }

        return $userAffiliation;
    }

    public function getTextAnswerWithSchoolResults(Survey $survey): array
    {
        return $this->dataTransformer->getTextAnswerWithSchoolResults($survey);
    }

    public function getTextAnswerWithTeamResults(Survey $survey): array
    {
        $textQuestionArray = [];
        $textQAarray = [];

        $semester = $survey->getSemester();

        // Get all text questions
        foreach ($survey->getSurveyQuestions() as $question) {
            if ($question->getType() === 'text') {
                $textQuestionArray[] = $question;
            }
        }

        // Collect text answers
        foreach ($textQuestionArray as $textQuestion) {
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = [];
            foreach ($textQuestion->getAnswers() as $answer) {
                $noTeamMemberships = $answer->getSurveyTaken()->getUser() === null
                    || count($answer->getSurveyTaken()->getUser()->getTeamMemberships()) === 0;

                if ($answer->getSurveyTaken()->getSurvey() !== $survey
                    || $noTeamMemberships) {
                    continue;
                }

                $user = $answer->getSurveyTaken()->getUser();
                $ua = $this->getUserAffiliationOfUserBySemester($user, $semester);
                $teamNames = $this->getTeamNamesAsString($ua);

                $textQAarray[$questionText][] = [
                    'answerText' => $answer->getAnswer(),
                    'teamName' => $teamNames,
                ];
            }
        }

        return $textQAarray;
    }

    public function getTeamNamesForSurveyTaker(SurveyTaken $taken): string
    {
        $user = $taken->getUser();
        $ua = $this->getUserAffiliationOfUserBySemester($user, $taken->getSurvey()->getSemester());

        return $this->getTeamNamesAsString($ua);
    }

    private function getTeamNamesAsString(array $teamNames): string
    {
        return $this->dataTransformer->formatTeamNames($teamNames);
    }

    private function getUserAffiliationOfUserBySemester(User $user, Semester $semester, $userAffiliation = []): array
    {
        $teamMemberships = $this->em->getRepository(TeamMembership::class)->findTeamMembershipsByUserAndSemester($user, $semester);

        foreach ($teamMemberships as $teamMembership) {
            $teamName = $teamMembership->getTeam()->getName();
            if (!in_array($teamName, $userAffiliation, true)) {
                $userAffiliation[] = $teamName;
            }
        }

        if (count($teamMemberships) === 0) {
            $userAffiliation[] = 'Ikke teammedlem';
        }

        return $userAffiliation;
    }

    public function surveyResultToJson(Survey $survey)
    {
        $userAffiliation = $this->getUserAffiliationOfSurveyAnswers($survey);
        $surveysTaken = $this->em->getRepository(SurveyTaken::class)->findAllTakenBySurvey($survey);

        $title = $this->getSurveyTargetAudienceString($survey);

        // Inject the school/team question into question array
        $userAffiliationQuestion = ['question_id' => 0, 'question_label' => $title, 'alternatives' => $userAffiliation];
        $survey_json = json_encode($survey);
        $survey_decode = json_decode($survey_json, true);
        $survey_decode['questions'][] = $userAffiliationQuestion;

        return ['survey' => $survey_decode, 'answers' => $surveysTaken];
    }

    public function toggleReservedFromPopUp(User $user)
    {
        $user->setReservedFromPopUp(!$user->getReservedFromPopUp());
        $user->setLastPopUpTime(new \DateTime('2000-01-01'));
        $this->em->persist($user);
        $this->em->flush();
    }

    public function getSurveyTargetAudienceString(Survey $survey): string
    {
        return $this->dataTransformer->getSurveyTargetAudienceString($survey);
    }

    public function surveyResultsToCsv(Survey $survey): string
    {
        // If the survey is for schools, it has an extra question about what school you are from
        // Else the survey is a team survey, in which case the team can be determined by the survey answer user id
        $schoolSurvey = $survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY || $survey->getTargetAudience() === Survey::$SCHOOL_SURVEY;
        if (!$schoolSurvey && $survey->getTargetAudience() !== Survey::$TEAM_SURVEY) {
            throw new \RuntimeException('Unrecognized survey target audience');
        }

        $surveysTaken = $this->em->getRepository(SurveyTaken::class)->findAllTakenBySurvey($survey);

        // Meta is the school or team the responder belongs to.
        $TARGET_AUDIENCE_COLUMN = 'targetaudiencecolumn';
        $target_audience_name = $schoolSurvey ? 'Skole' : 'Team';

        $questions = [$TARGET_AUDIENCE_COLUMN => $target_audience_name];
        foreach ($survey->getSurveyQuestions() as $question) {
            $questions[$question->getId()] = $question->getQuestion(); // The question text
        }
        // A 2d array of all completed surveys, each element being a map from question_id=>answer
        $csv_rows = [];
        foreach ($surveysTaken as $taken) {
            $csv_row = [];
            $answers = $taken->getSurveyAnswers();

            if ($schoolSurvey) {
                $csv_row[$TARGET_AUDIENCE_COLUMN] = $taken->getSchool()->getName();
            } else {
                $csv_row[$TARGET_AUDIENCE_COLUMN] = $this->getTeamNamesForSurveyTaker($taken);
            }

            foreach ($answers as $answer) {
                $question = $answer->getSurveyQuestion();
                $stored_as_text = $question->getType() !== 'check';
                if ($stored_as_text) {
                    $csv_row[$question->getId()] = $answer->getAnswer();
                } else {
                    $csv_row[$question->getId()] = join(',', $answer->getAnswerArray());
                }
            }

            $csv_rows[] = $csv_row;
        }

        return CsvUtil::csvFromTable($questions, $csv_rows);
    }
}
