<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;
use DateTime;
use PHPUnit\Framework\TestCase;

class ReceiptEntityUnitTest extends TestCase
{
    // Test the setUser() method
    public function testSetUser()
    {
        // New entities
        $user = new User();
        $receipt = new Receipt();

        // Use the setUser method
        $receipt->setUser($user);

        // Assert the result
        $this->assertEquals($user, $receipt->getUser());
    }

    public function testSetSubmitDate()
    {
        $dateTime = new DateTime();
        $receipt = new Receipt();

        $receipt->setSubmitDate($dateTime);

        $this->assertEquals($dateTime, $receipt->getSubmitDate());
    }

    public function testSetPicturePath()
    {
        $picturePath = 'test';
        $receipt = new Receipt();

        $receipt->setPicturePath($picturePath);

        $this->assertEquals($picturePath, $receipt->getPicturePath());
    }

    public function testSetDescription()
    {
        $sum = 13.0;
        $receipt = new Receipt();

        $receipt->setSum($sum);

        $this->assertEquals($sum, $receipt->getSum());
    }

    public function testSetStatus()
    {
        $receipt = new Receipt();

        $receipt->setStatus(Receipt::STATUS_PENDING);

        $this->assertEquals(Receipt::STATUS_PENDING, $receipt->getStatus());
    }
}
