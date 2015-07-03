<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\ApplicationStatistic;
use AppBundle\Entity\InterviewScore;


class InterviewScoreEntityUnitTest extends \PHPUnit_Framework_TestCase
{

    public function testSetApplicationStatistic()
    {

        $intScore = new InterviewScore();
        $appStat = new ApplicationStatistic();

        $intScore->setApplicationStatistic($appStat);

        $this->assertEquals($appStat, $intScore->getApplicationStatistic());

    }

    public function testSetExplanatoryPower()
    {

        $intScore = new InterviewScore();

        $intScore->setExplanatoryPower(3);

        $this->assertEquals(3, $intScore->getExplanatoryPower());

    }

    public function testSetRoleModel()
    {

        $intScore = new InterviewScore();

        $intScore->setRoleModel(3);

        $this->assertEquals(3, $intScore->getRoleModel());

    }

    public function testSetDrive()
    {

        $intScore = new InterviewScore();

        $intScore->setDrive(3);

        $this->assertEquals(3, $intScore->getDrive());

    }

    public function testSetTotalImpression()
    {

        $intScore = new InterviewScore();

        $intScore->setTotalImpression(3);

        $this->assertEquals(3, $intScore->getTotalImpression());

    }

    public function testGetSum()
    {

        $intScore = new InterviewScore();

        $intScore->setExplanatoryPower(3);
        $intScore->setDrive(3);
        $intScore->setRoleModel(3);
        $intScore->setTotalImpression(3);

        $this->assertEquals(12, $intScore->getSum());

    }

}