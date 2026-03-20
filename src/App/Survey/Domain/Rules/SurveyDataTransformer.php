<?php

namespace App\Survey\Domain\Rules;

use App\Survey\Infrastructure\Entity\Survey;

class SurveyDataTransformer
{
    public function getSurveyTargetAudienceString(Survey $survey): string
    {
        if ($survey->getTargetAudience() === Survey::$TEAM_SURVEY) {
            return 'Team';
        } elseif ($survey->getTargetAudience() === Survey::$ASSISTANT_SURVEY) {
            return 'Assistent';
        } elseif ($survey->getTargetAudience() === Survey::$SCHOOL_SURVEY) {
            return 'Skole';
        }

        return 'Andre';
    }

    public function getTextAnswerWithSchoolResults(Survey $survey): array
    {
        $textQuestionArray = [];
        $textQAarray = [];

        // Get all text questions
        foreach ($survey->getSurveyQuestions() as $question) {
            if ($question->getType() == 'text') {
                $textQuestionArray[] = $question;
            }
        }

        // Collect text answers
        foreach ($textQuestionArray as $textQuestion) {
            $questionText = $textQuestion->getQuestion();
            $textQAarray[$questionText] = [];
            foreach ($textQuestion->getAnswers() as $answer) {
                if ($answer->getSurveyTaken() === null
                    || $answer->getSurveyTaken()->getSurvey() !== $survey
                    || $answer->getSurveyTaken()->getSchool() === null) {
                    continue;
                }
                $textQAarray[$questionText][] = [
                    'answerText' => $answer->getAnswer(),
                    'schoolName' => $answer->getSurveyTaken()->getSchool()->getName(),
                ];
            }
        }

        return $textQAarray;
    }

    public function formatTeamNames(array $teamNames): string
    {
        $teamNames = implode(', ', $teamNames);
        $find = ',';
        $replace = ' og';
        $teamNames = strrev((string) preg_replace(strrev("/$find/"), strrev($replace), strrev($teamNames), 1));

        return $teamNames;
    }
}
