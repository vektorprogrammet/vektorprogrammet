<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\InterviewQuestion;
use AppBundle\Entity\InterviewQuestionAlternative;

class InterviewQuestionAlternativeEntityUnitTest extends \PHPUnit_Framework_TestCase
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
