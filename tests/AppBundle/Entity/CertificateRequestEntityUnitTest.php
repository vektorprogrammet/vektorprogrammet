<?php

namespace Tests\App\Entity;

use App\Operations\Infrastructure\Entity\CertificateRequest;
use App\Entity\User;
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
