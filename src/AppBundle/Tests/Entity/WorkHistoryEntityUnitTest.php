<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\WorkHistory;
use AppBundle\Entity\User;
use AppBundle\Entity\Team;
use AppBundle\Entity\Position;
use DateTime;

class WorkHistoryEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setStartDate function is working correctly
    /*public function testSetStartDate(){

        // New datetime variable
        $today = new DateTime("now");

        // new entity
        $wh = new WorkHistory();

        // Use the setStartDate method
        $wh->setStartDate($today);

        // Assert the result
        $this->assertEquals($today, $wh->getStartDate());

    }*/

    // Check whether the setEndDate function is working correctly
    /*	public function testSetEndDate(){

            // New datetime variable
            $today = new DateTime("now");

            // new entity
            $wh = new WorkHistory();

            // Use the setStartDate method
            $wh->setEndDate($today);

            // Assert the result
            $this->assertEquals($today, $wh->getEndDate());

        }*/

    // Check whether the setUser function is working correctly
    public function testSetUser()
    {

        // new entity
        $wh = new WorkHistory();

        // dummy entity
        $user = new User();
        $user->setFirstName('Per');

        // Use the setUser method
        $wh->setUser($user);

        // Assert the result
        $this->assertEquals($user, $wh->getUser());
    }

    // Check whether the setTeam function is working correctly
    public function testSetTeam()
    {

        // new entity
        $wh = new WorkHistory();

        // dummy entity
        $team = new Team();
        $team->setName('Hovedstyret');

        // Use the setTeam method
        $wh->setTeam($team);

        // Assert the result
        $this->assertEquals($team, $wh->getTeam());
    }

    // Check whether the setPosition function is working correctly
    public function testSetPosition()
    {

        // new entity
        $wh = new WorkHistory();

        // dummy entity
        $position = new Position();
        $position->setName('Superleder');

        // Use the setPosition method
        $wh->setPosition($position);

        // Assert the result
        $this->assertEquals($position, $wh->getPosition());
    }
}
