<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use AppBundle\Service\AdmissionStatistics;
use DateTime;
use PHPUnit\Framework\TestCase;

class AdmissionStatisticsTest extends TestCase
{
    /**
     * @var AdmissionStatistics
     */
    private $service;

    protected function setUp()
    {
        $this->service = new AdmissionStatistics();
    }

    public function testGenerateGraphDataFromSubscribersInSemester()
    {
        $semester = new Semester();
        $semester->setStartDate(new DateTime('2024-01-01'));
        $semester->setEndDate(new DateTime('2024-06-30'));

        $subscriber1 = new AdmissionSubscriber();
        $subscriber1->setTimestamp(new DateTime('2024-01-05'));

        $subscriber2 = new AdmissionSubscriber();
        $subscriber2->setTimestamp(new DateTime('2024-01-05'));

        $subscriber3 = new AdmissionSubscriber();
        $subscriber3->setTimestamp(new DateTime('2024-01-10'));

        $subscribers = [$subscriber1, $subscriber2, $subscriber3];

        $result = $this->service->generateGraphDataFromSubscribersInSemester($subscribers, $semester);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('2024-01-05', $result);
        $this->assertEquals(2, $result['2024-01-05']);
        $this->assertArrayHasKey('2024-01-10', $result);
        $this->assertEquals(1, $result['2024-01-10']);
    }

    public function testGenerateGraphDataFromApplicationsInAdmissionPeriod()
    {
        $semester = new Semester();
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setStartDate(new DateTime('2024-01-01'));
        $admissionPeriod->setEndDate(new DateTime('2024-03-31'));
        $admissionPeriod->setSemester($semester);

        $application1 = new Application();
        $application1->setCreated(new DateTime('2024-01-05'));

        $application2 = new Application();
        $application2->setCreated(new DateTime('2024-01-05'));

        $applications = [$application1, $application2];

        $result = $this->service->generateGraphDataFromApplicationsInAdmissionPeriod($applications, $admissionPeriod);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('2024-01-05', $result);
        $this->assertEquals(2, $result['2024-01-05']);
    }

    public function testGenerateCumulativeGraphDataFromApplicationsInAdmissionPeriod()
    {
        $semester = new Semester();
        $admissionPeriod = new AdmissionPeriod();
        $admissionPeriod->setStartDate(new DateTime('2024-01-01'));
        $admissionPeriod->setEndDate(new DateTime('2024-03-31'));
        $admissionPeriod->setSemester($semester);

        $application1 = new Application();
        $application1->setCreated(new DateTime('2024-01-05'));

        $application2 = new Application();
        $application2->setCreated(new DateTime('2024-01-10'));

        $applications = [$application1, $application2];

        $result = $this->service->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod($applications, $admissionPeriod);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('2024-01-05', $result);
        $this->assertArrayHasKey('2024-01-10', $result);
        
        // Cumulative data should have increasing values
        $dates = array_keys($result);
        sort($dates);
        $previousValue = 0;
        foreach ($dates as $date) {
            $this->assertGreaterThanOrEqual($previousValue, $result[$date]);
            $previousValue = $result[$date];
        }
    }
}

