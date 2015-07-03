<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Application;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\Interview;
use AppBundle\Entity\ApplicationStatistic;
use AppBundle\Entity\InterviewPractical;
use AppBundle\Entity\InterviewScore;
use AppBundle\Entity\Semester;

class ApplicationStatisticEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetGender()
    {

        $appStat = new ApplicationStatistic();

        $appStat->setGender(1);

        $this->assertEquals(1, $appStat->getGender());

    }

    public function testSetPreviousParticipation()
    {

        $appStat = new ApplicationStatistic();

        $appStat->setPreviousParticipation(true);

        $this->assertEquals(true, $appStat->getPreviousParticipation());

    }

    public function testSetAccepted()
    {

        $appStat = new ApplicationStatistic();

        $appStat->setAccepted(true);

        $this->assertEquals(true, $appStat->getAccepted());

    }

    public function testSetYearOfStudy()
    {

        $appStat = new ApplicationStatistic();

        $appStat->setYearOfStudy(3);

        $this->assertEquals(3, $appStat->getYearOfStudy(3));

    }

    public function testSetFieldOfStudy()
    {

        $appStat = new ApplicationStatistic();
        $fos = new FieldOfStudy();

        $appStat->setFieldOfStudy($fos);

        $this->assertEquals($fos, $appStat->getFieldOfStudy());

    }

    public function testSetSemester()
    {

        $appStat = new ApplicationStatistic();
        $semester = new Semester();

        $appStat->setSemester($semester);

        $this->assertEquals($semester, $appStat->getSemester());

    }

    public function testSetInterviewScore()
    {

        $appStat = new ApplicationStatistic();
        $intScore = new InterviewScore();

        $appStat->setInterviewScore($intScore);

        $this->assertEquals($intScore, $appStat->getInterviewScore());

    }

    public function testSetInterviewPractical()
    {

        $appStat = new ApplicationStatistic();
        $intPrac = new InterviewPractical();

        $appStat->setInterviewPractical($intPrac);

        $this->assertEquals($intPrac, $appStat->getInterviewPractical());

    }
}