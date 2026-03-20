<?php

namespace Tests\App\Entity;

use App\Interview\Infrastructure\Entity\InterviewQuestion;
use App\Interview\Infrastructure\Entity\InterviewQuestionAlternative;
use PHPUnit\Framework\TestCase;

class InterviewQuestionAlternativeEntityUnitTest extends TestCase
{
    public function testSetAlternative()
    {
        $alternative = new InterviewQuestionAlternative();

        $alternative->setAlternative('Test');

        $this->assertEquals('Test', $alternative->getAlternative());
    }

    public function testSetInterviewQuestion()
    {
        $alternative = new InterviewQuestionAlternative();
        $intQuestion = new InterviewQuestion();

        $alternative->setInterviewQuestion($intQuestion);

        $this->assertEquals($intQuestion, $alternative->getInterviewQuestion());
    }
}
