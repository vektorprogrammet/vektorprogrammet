<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\CertificateRequest;
use PHPUnit\Framework\TestCase;

class CertificateRequestEntityUnitTest extends TestCase
{
    // Check whether the setUser function is working correctly
    public function testSetUser()
    {

        // new entity
        $cr = new CertificateRequest();

        // dummy entity
        $user = new User();
        $user->setFirstName('Thomas');

        // Use the setUser method
        $cr->setUser($user);

        // Assert the result
        $this->assertEquals($user, $cr->getUser());
    }
}
