<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PasswordReset;
use AppBundle\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class PasswordResetEntityUnitTest extends TestCase
{
    // Check whether the setUser function is working correctly
    public function testSetUser()
    {
        $passwordReset = new PasswordReset();

        $user = new User();

        $passwordReset->setUser($user);

        $this->assertEquals($user, $passwordReset->getUser());
    }

    // Check whether the setDescription function is working correctly
    public function testSetHashedResetCode()
    {

        // new entity
        $passwordReset = new PasswordReset();

        $resetCode = hash('sha512', bin2hex(openssl_random_pseudo_bytes(12)), false);

        // Use the setDescription method
        $passwordReset->setHashedResetCode($resetCode);

        // Assert the result
        $this->assertEquals($resetCode, $passwordReset->getHashedResetCode());
    }

    // Check whether the setType function is working correctly
    public function testSetResetTime()
    {

        // new entity
        $passwordReset = new PasswordReset();

        $time = new DateTime();

        // Use the setType method
        $passwordReset->setResetTime($time);

        // Assert the result
        $this->assertEquals($time, $passwordReset->getResetTime());
    }
}
