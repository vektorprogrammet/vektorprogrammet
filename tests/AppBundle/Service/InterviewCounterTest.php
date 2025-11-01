<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Interview;
use AppBundle\Entity\InterviewScore;
use AppBundle\Entity\User;
use AppBundle\Service\InterviewCounter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class InterviewCounterTest extends KernelTestCase
{
    /**
     * @var InterviewCounter
     */
    private $service;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->service = $kernel->getContainer()->get(InterviewCounter::class);
    }

    public function testCountWithYes()
    {
        $applications = $this->createApplicationsWithSuitability([
            InterviewCounter::YES,
            InterviewCounter::MAYBE,
            InterviewCounter::YES,
            InterviewCounter::NO,
        ]);

        $count = $this->service->count($applications, InterviewCounter::YES);

        $this->assertEquals(2, $count);
    }

    public function testCountWithMaybe()
    {
        $applications = $this->createApplicationsWithSuitability([
            InterviewCounter::YES,
            InterviewCounter::MAYBE,
            InterviewCounter::MAYBE,
            InterviewCounter::NO,
        ]);

        $count = $this->service->count($applications, InterviewCounter::MAYBE);

        $this->assertEquals(2, $count);
    }

    public function testCountWithNo()
    {
        $applications = $this->createApplicationsWithSuitability([
            InterviewCounter::YES,
            InterviewCounter::NO,
            InterviewCounter::NO,
        ]);

        $count = $this->service->count($applications, InterviewCounter::NO);

        $this->assertEquals(2, $count);
    }

    public function testCountIgnoresApplicationsWithoutInterview()
    {
        $application1 = new Application();
        $application1->setInterview(null);

        $application2 = new Application();
        $interview = new Interview();
        $interviewScore = new InterviewScore();
        $interviewScore->setSuitableAssistant(InterviewCounter::YES);
        $interview->setInterviewScore($interviewScore);
        $application2->setInterview($interview);

        $applications = [$application1, $application2];

        $count = $this->service->count($applications, InterviewCounter::YES);

        $this->assertEquals(1, $count);
    }

    public function testCreateInterviewDistributions()
    {
        $admissionPeriod = new AdmissionPeriod();
        $interviewer1 = new User();
        $interviewer1->setFirstName('John');
        $interviewer1->setLastName('Doe');
        $interviewer2 = new User();
        $interviewer2->setFirstName('Jane');
        $interviewer2->setLastName('Smith');

        $applications = [];
        
        // Create 2 applications with interviewer1
        for ($i = 0; $i < 2; $i++) {
            $application = new Application();
            $interview = new Interview();
            $interview->setInterviewer($interviewer1);
            $application->setInterview($interview);
            $applications[] = $application;
        }

        // Create 1 application with interviewer2
        $application = new Application();
        $interview = new Interview();
        $interview->setInterviewer($interviewer2);
        $application->setInterview($interview);
        $applications[] = $application;

        $distributions = $this->service->createInterviewDistributions($applications, $admissionPeriod);

        $this->assertCount(2, $distributions);
    }

    private function createApplicationsWithSuitability(array $suitabilities): array
    {
        $applications = [];

        foreach ($suitabilities as $suitability) {
            $application = new Application();
            $interview = new Interview();
            $interviewScore = new InterviewScore();
            $interviewScore->setSuitableAssistant($suitability);
            $interview->setInterviewScore($interviewScore);
            $application->setInterview($interview);
            $applications[] = $application;
        }

        return $applications;
    }
}

