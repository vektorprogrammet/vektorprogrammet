<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Semester;

class SemesterEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setSemesterStartDate function is working correctly
    public function testSetSemesterStartDate()
    {
        // New datetime variable
        $today = new \DateTime('now');

        // new entity
        $semester = new Semester();

        // Use the setSemesterStartDate method
        $semester->setSemesterStartDate($today);

        // Assert the result
        $this->assertEquals($today, $semester->getSemesterStartDate());
    }

    // Check whether the setSemesterEndDate function is working correctly
    public function testSetSemesterEndDate()
    {
        // New datetime variable
        $today = new \DateTime('now');

        // new entity
        $semester = new Semester();

        // Use the setSemesterEndDate method
        $semester->setSemesterEndDate($today);

        // Assert the result
        $this->assertEquals($today, $semester->getSemesterEndDate());
    }
}
