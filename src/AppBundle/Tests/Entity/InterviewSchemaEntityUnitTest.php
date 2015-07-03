<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\InterviewQuestion;
use AppBundle\Entity\InterviewSchema;


class InterviewSchemaEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testAddInterviewQuestion()
    {

        $intSchema = new InterviewSchema();
        $intQuestion = new InterviewQuestion();

        $intSchema->addInterviewQuestion($intQuestion);

        $this->assertContainsOnly($intQuestion, $intSchema->getInterviewQuestions());

    }

    public function testRemoveInterviewQuestion()
    {

        $intSchema = new InterviewSchema();
        $intQuestion = new InterviewQuestion();

        $intSchema->addInterviewQuestion($intQuestion);

        $this->assertContainsOnly($intQuestion, $intSchema->getInterviewQuestions());

        $intSchema->removeInterviewQuestion($intQuestion);

        $this->assertNotContains($intQuestion, $intSchema->getInterviewQuestions());

    }

    public function testSetName()
    {

        $intSchema = new InterviewSchema();

        $intSchema->setName("Test");

        $this->assertEquals("Test", $intSchema->getName());

    }
}