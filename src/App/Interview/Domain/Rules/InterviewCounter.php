<?php

namespace App\Interview\Domain\Rules;

use App\Interview\Infrastructure\Entity\Interview;

class InterviewCounter
{
    public const YES = 'Ja';
    public const MAYBE = 'Kanskje';
    public const NO = 'Nei';

    /**
     * @param Interview[] $interviews
     *
     * @return int
     */
    public function count(array $interviews, string $suitable)
    {
        $count = 0;

        foreach ($interviews as $interview) {
            $suitableAssistant = $interview->getInterviewScore()->getSuitableAssistant();
            if ($suitableAssistant === $suitable) {
                ++$count;
            }
        }

        return $count;
    }
}
