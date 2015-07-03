<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewQuestion;


class InterviewAnswerEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetAnswer()
    {

        $intAnswer = new InterviewAnswer();

        $intAnswer->setAnswer("Test");

        $this->assertEquals("Test", $intAnswer->getAnswer());

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