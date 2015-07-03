<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\ApplicationStatistic;
use AppBundle\Entity\InterviewPractical;


class InterviewPracticalEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetPosition()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setPosition("2x2");

        $this->assertEquals("2x2", $intPrac->getPosition());

    }

    public function testSetMonday()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setMonday("Bra");

        $this->assertEquals("Bra", $intPrac->getMonday());

    }

    public function testSetTuesday()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setTuesday("Bra");

        $this->assertEquals("Bra", $intPrac->getTuesday());

    }

    public function testSetWednesday()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setWednesday("Bra");

        $this->assertEquals("Bra", $intPrac->getWednesday());

    }

    public function testSetThursday()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setThursday("Bra");

        $this->assertEquals("Bra", $intPrac->getThursday());

    }

    public function testSetFriday()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setFriday("Bra");

        $this->assertEquals("Bra", $intPrac->getFriday());

    }

    public function testSetSubstitute()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setSubstitute(true);

        $this->assertTrue($intPrac->getSubstitute());

    }

    public function testSetEnglish()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setEnglish(true);

        $this->assertTrue($intPrac->getEnglish());

    }

    public function testSetComment()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setComment("Test");

        $this->assertEquals("Test", $intPrac->getComment());

    }

    public function testSetHeardAboutFrom()
    {

        $intPrac = new InterviewPractical();

        $intPrac->setHeardAboutFrom("Stand");

        $this->assertEquals("Stand", $intPrac->getHeardAboutFrom());

    }

    public function testSetApplicationStatistic()
    {

        $intPrac = new InterviewPractical();
        $appStat = new ApplicationStatistic();

        $intPrac->setApplicationStatistic($appStat);

        $this->assertEquals($appStat, $intPrac->getApplicationStatistic());

    }

}