<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\User;
use AppBundle\Model\ApplicationStatus;
use AppBundle\Service\ApplicationManager;
use AppBundle\Type\InterviewStatusType;
use Tests\BaseKernelTestCase;

class ApplicationManagerTest extends BaseKernelTestCase
{
    /**
     * @var ApplicationManager
     */
    private $service;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->service = $kernel->getContainer()->get(ApplicationManager::class);
    }

    public function testGetApplicationStatusForActiveAssistant()
    {
        $user = new User();
        $user->setActiveAssistant(true);

        $application = new Application();
        $application->setUser($user);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::ASSIGNED_TO_SCHOOL, $status->getStatus());
    }

    public function testGetApplicationStatusForPreviousAssistant()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(true);

        $application = new Application();
        $application->setUser($user);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::INTERVIEW_COMPLETED, $status->getStatus());
    }

    public function testGetApplicationStatusNoInterview()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(false);

        $application = new Application();
        $application->setUser($user);
        $application->setInterview(null);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::APPLICATION_RECEIVED, $status->getStatus());
    }

    public function testGetApplicationStatusInterviewed()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(false);

        $interview = new Interview();
        $interview->setInterviewed(true);

        $application = new Application();
        $application->setUser($user);
        $application->setInterview($interview);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::INTERVIEW_COMPLETED, $status->getStatus());
    }

    public function testGetApplicationStatusPending()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(false);

        $interview = new Interview();
        $interview->setInterviewed(false);
        $interview->setInterviewStatus(InterviewStatusType::PENDING);

        $application = new Application();
        $application->setUser($user);
        $application->setInterview($interview);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::INVITED_TO_INTERVIEW, $status->getStatus());
    }

    public function testGetApplicationStatusAccepted()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(false);

        $interview = new Interview();
        $interview->setInterviewed(false);
        $interview->setInterviewStatus(InterviewStatusType::ACCEPTED);
        $interview->setRoom('Room A');
        $interview->setScheduled(new \DateTime('2024-01-15 10:00'));

        $application = new Application();
        $application->setUser($user);
        $application->setInterview($interview);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::INTERVIEW_ACCEPTED, $status->getStatus());
        $this->assertContains('Room A', $status->getDescription());
    }

    public function testGetApplicationStatusCancelled()
    {
        $user = new User();
        $user->setActiveAssistant(false);
        $user->setHasBeenAssistant(false);

        $interview = new Interview();
        $interview->setInterviewed(false);
        $interview->setInterviewStatus(InterviewStatusType::CANCELLED);

        $application = new Application();
        $application->setUser($user);
        $application->setInterview($interview);

        $status = $this->service->getApplicationStatus($application);

        $this->assertInstanceOf(ApplicationStatus::class, $status);
        $this->assertEquals(ApplicationStatus::CANCELLED, $status->getStatus());
    }
}

