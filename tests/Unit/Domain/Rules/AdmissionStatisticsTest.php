<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Rules;

use App\Admission\Domain\Rules\AdmissionStatistics;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Admission\Infrastructure\Entity\AdmissionSubscriber;
use App\Admission\Infrastructure\Entity\Application;
use PHPUnit\Framework\TestCase;

class AdmissionStatisticsTest extends TestCase
{
    private AdmissionStatistics $statistics;

    protected function setUp(): void
    {
        $this->statistics = new AdmissionStatistics();
    }

    private function makePeriod(\DateTime $start, \DateTime $end): AdmissionPeriod
    {
        $period = $this->createMock(AdmissionPeriod::class);
        $period->method('getStartDate')->willReturn($start);
        $period->method('getEndDate')->willReturn($end);

        return $period;
    }

    public function testGenerateGraphDataIsDeterministicGivenFixedNow(): void
    {
        $now = new \DateTime('2026-01-10');
        $period = $this->makePeriod(new \DateTime('2026-01-01'), new \DateTime('2026-01-31'));

        $result1 = $this->statistics->generateGraphDataFromApplicationsInAdmissionPeriod([], $period, $now);
        $result2 = $this->statistics->generateGraphDataFromApplicationsInAdmissionPeriod([], $period, $now);

        $this->assertSame($result1, $result2);
    }

    public function testGraphDataContainsDatesFromStartToNow(): void
    {
        $now = new \DateTime('2026-01-05');
        $period = $this->makePeriod(new \DateTime('2026-01-01'), new \DateTime('2026-01-31'));

        $result = $this->statistics->generateGraphDataFromApplicationsInAdmissionPeriod([], $period, $now);

        // Days from Jan 1 to Jan 5 = 4 days (0,1,2,3 — Jan 1 through Jan 4)
        $this->assertArrayHasKey('2026-01-01', $result);
        $this->assertArrayHasKey('2026-01-02', $result);
        $this->assertArrayHasKey('2026-01-03', $result);
        $this->assertArrayHasKey('2026-01-04', $result);
    }

    public function testApplicationCountIncrementsByDate(): void
    {
        $now = new \DateTime('2026-01-10');
        $period = $this->makePeriod(new \DateTime('2026-01-01'), new \DateTime('2026-01-31'));

        $app1 = $this->createMock(Application::class);
        $app1->method('getCreated')->willReturn(new \DateTime('2026-01-03'));

        $app2 = $this->createMock(Application::class);
        $app2->method('getCreated')->willReturn(new \DateTime('2026-01-03'));

        $app3 = $this->createMock(Application::class);
        $app3->method('getCreated')->willReturn(new \DateTime('2026-01-05'));

        $result = $this->statistics->generateGraphDataFromApplicationsInAdmissionPeriod(
            [$app1, $app2, $app3],
            $period,
            $now
        );

        $this->assertSame(2, $result['2026-01-03']);
        $this->assertSame(1, $result['2026-01-05']);
    }

    public function testCumulativeDataAccumulatesAcrossDates(): void
    {
        $now = new \DateTime('2026-01-10');
        $period = $this->makePeriod(new \DateTime('2026-01-01'), new \DateTime('2026-01-31'));

        $app1 = $this->createMock(Application::class);
        $app1->method('getCreated')->willReturn(new \DateTime('2026-01-02'));

        $app2 = $this->createMock(Application::class);
        $app2->method('getCreated')->willReturn(new \DateTime('2026-01-03'));

        $result = $this->statistics->generateCumulativeGraphDataFromApplicationsInAdmissionPeriod(
            [$app1, $app2],
            $period,
            $now
        );

        // Jan 2 = 1, Jan 3 = 1+1 = 2
        $this->assertSame(1, $result['2026-01-02']);
        $this->assertSame(2, $result['2026-01-03']);
    }

    public function testSubscriberGraphDataIsDeterministicGivenFixedNow(): void
    {
        $now = new \DateTime('2026-01-10');

        // Semester implements PeriodInterface
        $semester = $this->createMock(\App\Shared\Entity\Semester::class);
        $semester->method('getStartDate')->willReturn(new \DateTime('2026-01-01'));
        $semester->method('getEndDate')->willReturn(new \DateTime('2026-06-30'));

        $result1 = $this->statistics->generateGraphDataFromSubscribersInSemester([], $semester, $now);
        $result2 = $this->statistics->generateGraphDataFromSubscribersInSemester([], $semester, $now);

        $this->assertSame($result1, $result2);
    }

    public function testSubscriberCountIncrementsByDate(): void
    {
        $now = new \DateTime('2026-01-10');
        $semester = $this->createMock(\App\Shared\Entity\Semester::class);
        $semester->method('getStartDate')->willReturn(new \DateTime('2026-01-01'));
        $semester->method('getEndDate')->willReturn(new \DateTime('2026-06-30'));

        $sub = $this->createMock(AdmissionSubscriber::class);
        $sub->method('getTimestamp')->willReturn(new \DateTime('2026-01-05'));

        $result = $this->statistics->generateGraphDataFromSubscribersInSemester([$sub], $semester, $now);

        $this->assertSame(1, $result['2026-01-05']);
    }

    public function testPaddingDaysAddedWhenNowIsAfterEndDate(): void
    {
        // $now is 3 days after end date
        $now = new \DateTime('2026-01-13');
        $period = $this->makePeriod(new \DateTime('2026-01-01'), new \DateTime('2026-01-10'));

        $result = $this->statistics->generateGraphDataFromApplicationsInAdmissionPeriod([], $period, $now);

        // Should include days up to end date plus padding (3+2 = 5, capped at 6)
        // Start to end = 9 days, padding = 5 days → 14 total keys
        $this->assertGreaterThan(9, count($result));
    }
}
