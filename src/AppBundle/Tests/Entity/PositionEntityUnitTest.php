<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Position;

class PositionEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	// Check whether the setName function is working correctly
	public function testSetName(){
		
		// new entity
		$position = new Position();
		
		// Use the setName method 
		$position->setName("Leder");
		
		// Assert the result 
		$this->assertEquals("Leder", $position->getName());
		
	}

	
}

   
