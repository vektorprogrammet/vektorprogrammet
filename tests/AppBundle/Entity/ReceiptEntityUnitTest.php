<?php

namespace Tests\App\Entity;

use App\Operations\Infrastructure\Entity\Receipt;
use App\Identity\Infrastructure\Entity\User;
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
        $dateTime = new \DateTime();
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

    public function testValidReceiptStatusTransitionPendingToRefunded()
    {
        $receipt = new Receipt();
        $receipt->setStatus(Receipt::STATUS_REFUNDED);
        $this->assertEquals(Receipt::STATUS_REFUNDED, $receipt->getStatus());
    }

    public function testValidReceiptStatusTransitionRejectedToPending()
    {
        $receipt = new Receipt();
        $receipt->setStatus(Receipt::STATUS_REJECTED);
        $receipt->setStatus(Receipt::STATUS_PENDING);
        $this->assertEquals(Receipt::STATUS_PENDING, $receipt->getStatus());
    }

    public function testInvalidReceiptStatusTransitionRefundedToPendingThrows()
    {
        $receipt = new Receipt();
        $receipt->setStatus(Receipt::STATUS_REFUNDED);
        // REFUNDED is terminal
        $this->expectException(\InvalidArgumentException::class);
        $receipt->setStatus(Receipt::STATUS_PENDING);
    }

    public function testReceiptSelfTransitionAllowed()
    {
        $receipt = new Receipt();
        // pending -> pending should not throw
        $receipt->setStatus(Receipt::STATUS_PENDING);
        $this->assertEquals(Receipt::STATUS_PENDING, $receipt->getStatus());
    }
}
