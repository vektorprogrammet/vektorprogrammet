<?php

namespace Tests\App\Entity;

use App\Entity\Interview;
use App\Entity\InterviewAnswer;
use App\Entity\InterviewQuestion;
use PHPUnit\Framework\TestCase;

class InterviewAnswerEntityUnitTest extends TestCase
{
    public function testSetAnswer()
    {
        $intAnswer = new InterviewAnswer();

        $intAnswer->setAnswer('Test');

        $this->assertEquals('Test', $intAnswer->getAnswer());
    }

    public function testSetInterview()
    {
        $intAnswer = new InterviewAnswer();
        $interview = new Interview();

        $intAnswer->setInterview($interview);

        $this->assertEquals($interview, $intAnswer->getInterview());
    }

    public function testSetInterviewQuestion()
    {
        $intAnswer = new InterviewAnswer();
        $intQuestion = new InterviewQuestion();

        $intAnswer->setInterviewQuestion($intQuestion);

        $this->assertEquals($intQuestion, $intAnswer->getInterviewQuestion());
    }
}
