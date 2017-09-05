<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Receipt;

class ReceiptEntityUnitTest extends \PHPUnit_Framework_TestCase
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

    public function testSetId()
    {
        $id = 99999;
        $receipt = new Receipt();

        $receipt->setId($id);

        $this->assertEquals($id, $receipt->getId());
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

    public function testSetActive()
    {
        $active = true;
        $receipt = new Receipt();

        $receipt->setActive($active);

        $this->assertEquals($active, $receipt->isActive());
    }
}
