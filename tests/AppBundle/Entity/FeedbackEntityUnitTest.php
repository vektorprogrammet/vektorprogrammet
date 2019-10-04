<?php

use AppBundle\Entity\Feedback;
use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class FeedbackEntityUnitTest extends TestCase
{
    // Check whether the setTitle function is working correctly
    public function testSetTitle()
    {
        // new entity
        $feedback = new Feedback();

        // Use the setName method
        $feedback->setTitle("Test-title");

        // Assert the result
        $this->assertEquals('Test-title', $feedback->getTitle());
    }

    //Testing the setDescription function
    public function testSetDescription()
    {
        // new entity
        $feedback = new Feedback();

        // Use the setName method
        $feedback->setDescription("Test description 123");

        // Assert the result
        $this->assertEquals("Test description 123", $feedback->getDescription());
    }

    //Testing the setType function
    public function testSetType()
    {
        // new entity
        $feedback = new Feedback();

        // Use the setName method
        $feedback->setType("my new type");

        // Assert the result
        $this->assertEquals("my new type", $feedback->getType());
    }
    
    //Testing the setUser function
    public function testSetUser()
    {
        // new entity
        $feedback = new Feedback();
        $user = new User();

        // Use the setName method
        $feedback->setUser($user);

        // Assert the result
        $this->assertEquals($user, $feedback->getUser());
    }
}
