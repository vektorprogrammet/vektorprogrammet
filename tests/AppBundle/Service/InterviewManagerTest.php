<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewQuestion;
use AppBundle\Entity\InterviewAnswer;
use AppBundle\Entity\InterviewSchema;
use AppBundle\Entity\User;
use AppBundle\Mailer\MailerInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\InterviewManager;
use AppBundle\Sms\SmsSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class InterviewManagerTest extends TestCase
{
    /**
     * @var InterviewManager
     */
    private $service;

    /**
     * @var TokenStorageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $authorizationChecker;

    protected function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);
        $twig = $this->createMock(Environment::class);
        $logger = $this->createMock(LoggerInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $router = $this->createMock(RouterInterface::class);
        $smsSender = $this->createMock(SmsSenderInterface::class);

        $this->service = new InterviewManager(
            $this->tokenStorage,
            $this->authorizationChecker,
            $mailer,
            $twig,
            $logger,
            $em,
            $router,
            $smsSender
        );
    }

    public function testLoggedInUserCanSeeInterviewWithTeamLeader()
    {
        $interview = new Interview();
        $user = new User();
        $token = $this->createMock(TokenInterface::class);

        $token->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->willReturn($token);

        $this->authorizationChecker->expects($this->once())
            ->method('isGranted')
            ->with(Roles::TEAM_LEADER)
            ->willReturn(true);

        $result = $this->service->loggedInUserCanSeeInterview($interview);

        $this->assertTrue($result);
    }

    public function testInitializeInterviewAnswers()
    {
        $interview = new Interview();
        $schema = new InterviewSchema();
        $question1 = new InterviewQuestion();
        $question2 = new InterviewQuestion();
        $schema->addInterviewQuestion($question1);
        $schema->addInterviewQuestion($question2);
        $interview->setInterviewSchema($schema);

        $result = $this->service->initializeInterviewAnswers($interview);

        $this->assertSame($interview, $result);
        $this->assertCount(2, $interview->getInterviewAnswers());
    }

    public function testAssignInterviewerToApplication()
    {
        $interviewer = new User();
        $applicant = new User();
        $application = new Application();
        $application->setUser($applicant);

        $this->service->assignInterviewerToApplication($interviewer, $application);

        $interview = $application->getInterview();
        $this->assertNotNull($interview);
        $this->assertSame($interviewer, $interview->getInterviewer());
        $this->assertSame($applicant, $interview->getUser());
        $this->assertFalse($interview->getInterviewed());
    }

    public function testAssignInterviewerToApplicationWithExistingInterview()
    {
        $interviewer = new User();
        $applicant = new User();
        $application = new Application();
        $application->setUser($applicant);
        $existingInterview = new Interview();
        $application->setInterview($existingInterview);

        $this->service->assignInterviewerToApplication($interviewer, $application);

        $interview = $application->getInterview();
        $this->assertSame($existingInterview, $interview);
        $this->assertSame($interviewer, $interview->getInterviewer());
    }
}

