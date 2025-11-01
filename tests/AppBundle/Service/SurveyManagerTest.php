<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyAnswer;
use AppBundle\Entity\SurveyQuestion;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\User;
use AppBundle\Service\SurveyManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class SurveyManagerTest extends TestCase
{
    /**
     * @var SurveyManager
     */
    private $service;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $em;

    protected function setUp()
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->service = new SurveyManager($this->em);
    }

    public function testInitializeSurveyTaken()
    {
        $survey = new Survey();
        $question1 = new SurveyQuestion();
        $question2 = new SurveyQuestion();
        $survey->addSurveyQuestion($question1);
        $survey->addSurveyQuestion($question2);

        $result = $this->service->initializeSurveyTaken($survey);

        $this->assertInstanceOf(SurveyTaken::class, $result);
        $this->assertSame($survey, $result->getSurvey());
        $this->assertCount(2, $result->getSurveyAnswers());
    }

    public function testInitializeUserSurveyTaken()
    {
        $survey = new Survey();
        $user = new User();
        $user->setEmail('test@example.com');

        $result = $this->service->initializeUserSurveyTaken($survey, $user);

        $this->assertInstanceOf(SurveyTaken::class, $result);
        $this->assertSame($survey, $result->getSurvey());
        $this->assertSame($user, $result->getUser());
    }

    public function testPredictSurveyTakenAnswersWithNoPreviousSurveys()
    {
        $survey = new Survey();
        $surveyTaken = new SurveyTaken();
        $surveyTaken->setSurvey($survey);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())
            ->method('findAllTakenBySurvey')
            ->with($survey)
            ->willReturn([]);

        $this->em->expects($this->once())
            ->method('getRepository')
            ->with(SurveyTaken::class)
            ->willReturn($repo);

        $result = $this->service->predictSurveyTakenAnswers($surveyTaken);

        $this->assertSame($surveyTaken, $result);
    }
}

