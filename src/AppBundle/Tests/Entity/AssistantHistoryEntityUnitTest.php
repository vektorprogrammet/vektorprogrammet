<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\User;
use AppBundle\Entity\Semester;
use AppBundle\Entity\School;

class AssistantHistoryEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	// Check whether the setUser function is working correctly
	public function testSetUser(){
		
		// new assistantHistory entity
		$assistantHistory = new AssistantHistory();
		
		// New user entity
		$user = new User();
		
		// Fill in some random data to the user entity 
		$user->setFirstName("Per");
		$user->setLastName("Olsen");
		$user->setUsername("petjo");
		
		// Use the setUser method for assistantHistory 
		$assistantHistory->setUser($user);
		
		// Assert the result 
		$this->assertEquals("petjo", $assistantHistory->getUser()->getUsername());
		
	}
	
	// Check whether the setSemester function is working correctly
	public function testSetSemester(){
		
		// new assistantHistory entity
		$assistantHistory = new AssistantHistory();
		
		// new semester entity 
		$semester = new Semester();
		
		// Set some random datat to the entity 
		$semester->setSemesterTime('Vår');

		// Set the entity to assistantHistory
		$assistantHistory->setSemester($semester);
		
		// Assert the result 
		$this->assertEquals($semester->getSemesterTime(), $assistantHistory->getSemester()->getSemesterTime());
		
	}
	
	// Check whether the setSchool function is working correctly
	public function testSetSchool(){
		
		// new assistantHistory entity
		$assistantHistory = new AssistantHistory();
		
		// new school entity 
		$school = new School();
		
		// Set some random datat to the entity 
		$school->setName("Heggen");
		
		// Set the entity to assistantHistory
		$assistantHistory->setSchool($school);
		
		// Assert the result 
		$this->assertEquals($school->getName(), $assistantHistory->getSchool()->getName());
		
	}
	
	// Check whether the setWorkdays function is working correctly
	public function testSetWorkdays(){
	
		// new assistantHistory entity
		$assistantHistory = new AssistantHistory();
		
		// Set the workdays of the entity 
		$assistantHistory->setWorkdays("5");
		
		// Assert the result 
		$this->assertEquals("5", $assistantHistory->getWorkdays());

	}
	
	
	
}

   
