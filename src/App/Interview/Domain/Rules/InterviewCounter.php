<?php

declare(strict_types=1);

namespace App\Interview\Domain\Rules;

use App\Interview\Domain\ValueObjects\Suitability;
use App\Interview\Infrastructure\Entity\Interview;

class InterviewCounter
{
    /**
     * @param Interview[] $interviews
     *
     * @return int
     */
    public function count(array $interviews, Suitability $suitable)
    {
        $count = 0;

        foreach ($interviews as $interview) {
            $score = $interview->getInterviewScore();
            if ($score === null) {
                continue;
            }
            if ($score->getSuitableAssistant() === $suitable->value) {
                ++$count;
            }
        }

        return $count;
    }
}
