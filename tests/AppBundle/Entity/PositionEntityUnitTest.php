<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Position;
use PHPUnit\Framework\TestCase;

class PositionEntityUnitTest extends TestCase
{
    // Check whether the setName function is working correctly
    public function testSetName()
    {

        // new entity
        $position = new Position();

        // Use the setName method
        $position->setName('Leder');

        // Assert the result
        $this->assertEquals('Leder', $position->getName());
    }
}
