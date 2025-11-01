<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Service\Contract\ApplicationDataInterface;
use AppBundle\Service\Contract\SlackMessengerInterface;
use AppBundle\Service\InterviewNotificationManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

class InterviewNotificationManagerTest extends TestCase
{
    /**
     * @var InterviewNotificationManager
     */
    private $service;

    /**
     * @var SlackMessengerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $slackMessenger;

    /**
     * @var ApplicationDataInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $applicationData;

    /**
     * @var RouterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $router;

    protected function setUp()
    {
        $this->slackMessenger = $this->createMock(SlackMessengerInterface::class);
        $this->applicationData = $this->createMock(ApplicationDataInterface::class);
        $this->router = $this->createMock(RouterInterface::class);

        $this->service = new InterviewNotificationManager(
            $this->slackMessenger,
            $this->applicationData,
            $this->router
        );
    }

    public function testSendApplicationCountNotification()
    {
        $department = new Department();
        $department->setShortName('NTNU');
        $department->setId(1);

        $semester = new Semester();
        $semester->setId(1);

        $this->applicationData->expects($this->once())
            ->method('getInterviewedAssistantsCount')
            ->willReturn(10);

        $this->applicationData->expects($this->once())
            ->method('getInterviewsLeftCount')
            ->willReturn(5);

        $this->router->expects($this->once())
            ->method('generate')
            ->with(
                'applications_show_interviewed',
                ['department' => 1, 'semester' => 1],
                Router::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/interviews');

        $this->slackMessenger->expects($this->once())
            ->method('notify')
            ->with($this->stringContains('NTNU'));

        $this->service->sendApplicationCountNotification($department, $semester);
    }

    public function testSendInterviewsCompletedNotification()
    {
        $department = new Department();
        $department->setShortName('NTNU');
        $department->setId(1);

        $semester = new Semester();
        $semester->setId(1);

        $this->applicationData->expects($this->once())
            ->method('setDepartment')
            ->with($department);

        $this->applicationData->expects($this->once())
            ->method('getTotalInterviewsCount')
            ->willReturn(15);

        $this->applicationData->expects($this->once())
            ->method('getApplicationCount')
            ->willReturn(20);

        $this->applicationData->expects($this->once())
            ->method('getPreviousParticipationCount')
            ->willReturn(5);

        $this->applicationData->expects($this->once())
            ->method('getInterviewedAssistantsCount')
            ->willReturn(15);

        $this->applicationData->expects($this->once())
            ->method('getCancelledInterviewsCount')
            ->willReturn(2);

        $this->applicationData->expects($this->once())
            ->method('getMaleCount')
            ->willReturn(10);

        $this->applicationData->expects($this->once())
            ->method('getMalePercentage')
            ->willReturn(50.0);

        $this->applicationData->expects($this->once())
            ->method('getFemaleCount')
            ->willReturn(10);

        $this->applicationData->expects($this->once())
            ->method('getFemalePercentage')
            ->willReturn(50.0);

        $this->router->expects($this->once())
            ->method('generate')
            ->willReturn('http://example.com/interviews');

        $this->slackMessenger->expects($this->exactly(3))
            ->method('notify');

        $this->service->sendInterviewsCompletedNotification($department, $semester);
    }
}

