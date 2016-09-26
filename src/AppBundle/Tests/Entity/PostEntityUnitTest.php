<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Post;
use AppBundle\Entity\Thread;
use AppBundle\Entity\User;
use DateTime;

class PostEntityUnitTest extends \PHPUnit_Framework_TestCase
{
    // Check whether the setSubject function is working correctly
    public function testSetSubject()
    {

        // new entity
        $post = new Post();

        // Use the setSubject method
        $post->setSubject('One subject');

        // Assert the result
        $this->assertEquals('One subject', $post->getSubject());
    }

    // Check whether the setDatetime function is working correctly
    public function testSetDatetime()
    {

        // new entity
        $post = new Post();

        $datetime = new DateTime('now');

        // Use the setDatetime method
        $post->setDatetime($datetime);

        // Assert the result
        $this->assertEquals($datetime, $post->getDatetime());
    }

    // Check whether the setText function is working correctly
    public function testSetText()
    {

        // new entity
        $post = new Post();

        // Use the setText method
        $post->setText('One text');

        // Assert the result
        $this->assertEquals('One text', $post->getText());
    }

    // Check whether the setThread function is working correctly
    public function testSetThread()
    {

        // new entity
        $post = new Post();

        // dummy entity
        $thread = new Thread();
        $thread->setSubject('hehe');

        // Use the setThread method
        $post->setThread($thread);

        // Assert the result
        $this->assertEquals($thread, $post->getThread());
    }

    // Check whether the setUser function is working correctly
    public function testSetUser()
    {

        // new entity
        $post = new Post();

        // dummy entity
        $user = new User();
        $user->setFirstName('Ole');

        // Use the setUser method
        $post->setUser($user);

        // Assert the result
        $this->assertEquals($user, $post->getUser());
    }
}
