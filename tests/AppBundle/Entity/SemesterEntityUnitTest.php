<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Semester;
use PHPUnit\Framework\TestCase;

class SemesterEntityUnitTest extends TestCase
{
    public function testSetYear()
    {
        $semester = new Semester();
        $year = 1980;
        $semester->setYear($year);
        $this->assertEquals($year, $semester->getYear());
    }

    public function testSetSemesterTime()
    {
        $semester = new Semester();
        $semesterTime = 'Vår';
        $semester->setSemesterTime($semesterTime);
        $this->assertEquals($semesterTime, $semester->getSemesterTime());
    }

    public function testGetSemesterStartDate()
    {
        $semester = new Semester();
        $semesterTime = 'Vår';
        $year = 1980;

        $startMonth = $semesterTime == 'Vår' ? '01' : '08';
        $expectedDate = date_create($year.'-'.$startMonth.'-01 00:00:00');
        $semester->setSemesterTime($semesterTime);
        $semester->setYear($year);

        $this->assertEquals($expectedDate, $semester->getStartDate());
    }

    public function testGetSemesterEndDate()
    {
        $semester = new Semester();
        $semesterTime = 'Vår';
        $year = 1980;

        $endMonth = $semesterTime == 'Vår' ? '07' : '12';
        $expectedDate = date_create($year.'-'.$endMonth.'-31 23:59:59');
        $semester->setSemesterTime($semesterTime);
        $semester->setYear($year);

        $this->assertEquals($expectedDate, $semester->getEndDate());
    }

    public function testIsBefore()
    {
        $semester = (new Semester())
            ->setYear(1980)
            ->setSemesterTime('Vår');


        /***** ASSERTIONS FOR DIFFERENT YEARS *****/
        // Assert that null is before
        $this->assertTrue($semester->isBefore(null));

        // Assert that we are before later semesters
        $this->assertTrue(
            $semester->isBefore(
                (new Semester())
                    ->setYear(1990)
                    ->setSemesterTime('Vår'))
        );

        // Assert that we are not before previous semesters
        $this->assertFalse(
            $semester->isBefore(
                (new Semester())
                    ->setYear(1975)
                    ->setSemesterTime('Høst'))
        );


        /***** ASSERTIONS FOR EQUAL YEARS *****/
        $year = 1980;
        // isBefore compares weakly: Assert that equal semesters are before
        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBefore(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBefore(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBefore(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBefore(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );
    }

    public function testIsAfter()
    {
        $semester = (new Semester())
            ->setYear(1980)
            ->setSemesterTime('Vår');

         /***** ASSERTIONS FOR DIFFERENT YEARS *****/
        // Assert that null is after
        $this->assertTrue($semester->isAfter(null));

        // Assert that we are after previous semesters
        $this->assertTrue(
            $semester->isAfter(
                (new Semester())
                    ->setYear(1975)
                    ->setSemesterTime('Høst'))
        );

        // Assert that we are not after later semesters
        $this->assertFalse(
            $semester->isAfter(
                (new Semester())
                    ->setYear(1990)
                    ->setSemesterTime('Vår'))
        );

        /***** ASSERTIONS FOR EQUAL YEARS *****/
        $year = 1980;
        // isAfter compares weakly: Assert that equal semesters are after
        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isAfter(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isAfter(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isAfter(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        // isAfter compares weakly: Assert that equal semesters are after
        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isAfter(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );
    }

    public function testInBetween()
    {
        $semester = (new Semester())
            ->setYear(1980)
            ->setSemesterTime('Høst');

        /***** ASSERTIONS FOR DIFFERENT YEARS *****/
        // null and null corresponds to the range (-inf, inf)
        $this->assertTrue(
            $semester->isBetween(null, null)
        );

        // One of the ends being null means it extends to inf
        $this->assertTrue(
            $semester->isBetween(
                (new Semester())
                    ->setYear(1975)
                    ->setSemesterTime('Høst')
            , null)
        );
        $this->assertTrue(
            $semester->isBetween(null,
                (new Semester())
                    ->setYear(1985)
                    ->setSemesterTime('Høst')
            )
        );

        $this->assertTrue(
            $semester->isBetween(
                (new Semester())
                    ->setYear(1975)
                    ->setSemesterTime('Høst'),
                (new Semester())
                    ->setYear(1985)
                    ->setSemesterTime('Høst')
            )
        );

        $this->assertFalse(
            $semester->isBetween(
                (new Semester())
                    ->setYear(1985)
                    ->setSemesterTime('Høst'),
                (new Semester())
                    ->setYear(1975)
                    ->setSemesterTime('Høst')
            )
        );


        /***** ASSERTIONS FOR EQUAL YEARS *****/
        $year = 1980;

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );
        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Vår')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

        $this->assertFalse(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Vår')
                )
        );

        $this->assertTrue(
            (new Semester())
                ->setYear($year)
                ->setSemesterTime('Høst')
                ->isBetween(
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst'),
                    (new Semester())
                        ->setYear($year)
                        ->setSemesterTime('Høst')
                )
        );

    }
}
