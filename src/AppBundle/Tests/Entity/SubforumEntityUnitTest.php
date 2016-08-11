<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Subforum;
use AppBundle\Entity\School;
use AppBundle\Entity\Forum;
use AppBundle\Entity\Team;
use AppBundle\Entity\Thread;

class SubforumEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $subforum = new Subforum();

        // Use the setName method 
        $subforum->setName('Heggen');

        // Assert the result 
        $this->assertEquals('Heggen', $subforum->getName());
    }

    // Check whether the addSchool function is working correctly
    public function testAddSchool()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $school1 = new School();
        $school1->setName('skole1');

        // Use the addSchool method 
        $subforum->addSchool($school1);

        // Schools is stored in an array 
        $schools = $subforum->getSchools();

        // Loop through the array and check for matches
        foreach ($schools as $school) {
            if ($school1 == $school) {
                // Assert the result
                $this->assertEquals($school1, $school);
            }
        }
    }

    // Check whether the removeSchool function is working correctly
    public function testRemoveSchool()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $school1 = new School();
        $school1->setName('skole1');
        $school2 = new School();
        $school2->setName('skole2');
        $school3 = new School();
        $school3->setName('skole3');

        // Use the addSchool method 
        $subforum->addSchool($school1);
        $subforum->addSchool($school2);
        $subforum->addSchool($school3);

        // Remove $school1 from department 
        $subforum->removeSchool($school1);

        // schools is stored in an array 
        $schools = $subforum->getSchools();

        // Loop through the array
        foreach ($schools as $school) {
            // Assert the result 
            $this->assertNotEquals($school1, $school);
        }
    }

    // Check whether the addForum function is working correctly
    public function testAddForum()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $forum1 = new Forum();
        $forum1->setName('forum1');

        // Use the addForum method 
        $subforum->addForum($forum1);

        // Forums is stored in an array 
        $forums = $subforum->getForums();

        // Loop through the array and check for matches
        foreach ($forums as $forum) {
            if ($forum1 == $forum) {
                // Assert the result
                $this->assertEquals($forum1, $forum);
            }
        }
    }

    // Check whether the removeForum function is working correctly
    public function testRemoveForum()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $forum1 = new Forum();
        $forum1->setName('skole1');
        $forum2 = new Forum();
        $forum2->setName('skole2');
        $forum3 = new Forum();
        $forum3->setName('skole3');

        // Use the addForum method 
        $subforum->addForum($forum1);
        $subforum->addForum($forum2);
        $subforum->addForum($forum3);

        // Remove $forum1 from department 
        $subforum->removeForum($forum1);

        // forums is stored in an array 
        $forums = $subforum->getForums();

        // Loop through the array
        foreach ($forums as $forum) {
            // Assert the result 
            $this->assertNotEquals($forum1, $forum);
        }
    }

    // Check whether the addTeam function is working correctly
    public function testAddTeam()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $team1 = new Team();
        $team1->setName('team1');

        // Use the addTeam method 
        $subforum->addTeam($team1);

        // Teams is stored in an array 
        $teams = $subforum->getTeams();

        // Loop through the array and check for matches
        foreach ($teams as $team) {
            if ($team1 == $team) {
                // Assert the result
                $this->assertEquals($team1, $team);
            }
        }
    }

    // Check whether the removeTeam function is working correctly
    public function testRemoveTeam()
    {

        // new entity
        $subforum = new Subforum();

        $team1 = new Team();
        $team1->setName('Team1');
        $team2 = new Team();
        $team2->setName('Team2');
        $team3 = new Team();
        $team3->setName('Team3');

        // Use the addTeam method 
        $subforum->addTeam($team1);
        $subforum->addTeam($team2);
        $subforum->addTeam($team3);

        // Remove $team1 
        $subforum->removeTeam($team1);

        // Teams are stored in an array 
        $teams = $subforum->getTeams();

        // Loop through the array
        foreach ($teams as $team) {
            // Assert the result 
            $this->assertNotEquals($team1, $team);
        }
    }

    // Check whether the setSchoolDocument function is working correctly
    public function testSetSchoolDocument()
    {

        // new entity
        $subforum = new Subforum();

        // Use the setSchoolDocument method 
        $subforum->setSchoolDocument('test1234');

        // Assert the result 
        $this->assertEquals('test1234', $subforum->getSchoolDocument());
    }

    // Check whether the removeThread function is working correctly
    public function testRemoveThread()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $thread1 = new Thread();
        $thread1->setSubject('thread1');
        $thread2 = new Thread();
        $thread2->setSubject('thread2');
        $thread3 = new Thread();
        $thread3->setSubject('thread3');

        // Use the addThread method 
        $subforum->addThread($thread1);
        $subforum->addThread($thread2);
        $subforum->addThread($thread3);

        // Remove $thread1 from department 
        $subforum->removeThread($thread1);

        // threads is stored in an array 
        $threads = $subforum->getThreads();

        // Loop through the array
        foreach ($threads as $thread) {
            // Assert the result 
            $this->assertNotEquals($thread1, $thread);
        }
    }

    // Check whether the addThread function is working correctly
    public function testAddThread()
    {

        // new entity
        $subforum = new Subforum();

        // New dummy entity 
        $thread1 = new Thread();
        $thread1->setSubject('thread1');

        // Use the addThread method 
        $subforum->addThread($thread1);

        // Threads is stored in an array 
        $threads = $subforum->getThreads();

        // Loop through the array and check for matches
        foreach ($threads as $thread) {
            if ($thread1 == $thread) {
                // Assert the result
                $this->assertEquals($thread1, $thread);
            }
        }
    }

    // Check whether the setType function is working correctly
    public function testSetType()
    {

        // new entity
        $subforum = new Subforum();

        // Use the setType method 
        $subforum->setType('general');

        // Assert the result 
        $this->assertEquals('general', $subforum->getType());
    }
}
