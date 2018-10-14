<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\InterviewScore;
use PHPUnit\Framework\TestCase;

class InterviewScoreEntityUnitTest extends TestCase
{
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

    public function testGetSum()
    {
        $intScore = new InterviewScore();

        $intScore->setExplanatoryPower(3);
        $intScore->setRoleModel(3);

        $this->assertEquals(6, $intScore->getSum());
    }
}
