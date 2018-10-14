<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\SupportTicket;
use PHPUnit\Framework\TestCase;

class ContactEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new assistantHistory entity
        $contact = new SupportTicket();

        // Use the setName method
        $contact->setName('Grete');

        // Assert the result
        $this->assertEquals('Grete', $contact->getName());
    }

    // Check whether the setEmail function is working correctly
    public function testSetEmail()
    {

        // new assistantHistory entity
        $contact = new SupportTicket();

        // Use the setEmail method
        $contact->setEmail('Grete@mail.com');

        // Assert the result
        $this->assertEquals('Grete@mail.com', $contact->getEmail());
    }

    // Check whether the setSubject function is working correctly
    public function testSetSubject()
    {

        // new assistantHistory entity
        $contact = new SupportTicket();

        // Use the setSubject method
        $contact->setSubject('asdf');

        // Assert the result
        $this->assertEquals('asdf', $contact->getSubject());
    }

    // Check whether the setBody function is working correctly
    public function testSetBody()
    {

        // new assistantHistory entity
        $contact = new SupportTicket();

        // Use the setBody method
        $contact->setBody('This is a long dummy text that is suppose to be somewhat long and somewhat short');

        // Assert the result
        $this->assertEquals('This is a long dummy text that is suppose to be somewhat long and somewhat short', $contact->getBody());
    }
}
