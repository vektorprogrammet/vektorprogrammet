<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Department;
use \DateTime;

class SemesterEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	
	
	// Check whether the setAdmissionStartDate function is working correctly
	public function testSetAdmissionStartDate(){
		
		// New datetime variable 
		$today = new DateTime("now");
		
		// new entity
		$semester = new Semester();
		
		// Use the setAdmissionStartDate method 
		$semester->setAdmissionStartDate($today);
		
		// Assert the result 
		$this->assertEquals($today, $semester->getAdmissionStartDate());
		
	}
	
	// Check whether the setAdmissionEndDate function is working correctly
	public function testSetAdmissionEndDate(){
		
		// New datetime variable 
		$today = new DateTime("now");
		
		// new entity
		$semester = new Semester();
		
		// Use the setAdmissionEndDate method 
		$semester->setAdmissionEndDate($today);
		
		// Assert the result 
		$this->assertEquals($today, $semester->getAdmissionEndDate());
		
	}
	
	// Check whether the setDepartment function is working correctly
	public function testSetDepartment(){
		
		// new entity
		$semester = new Semester();
		
		// Dummy entity
		$department = new Department();
		$department->setName("NTNU");
		
		// Use the setDepartment method 
		$semester->setDepartment($department);
		
		// Assert the result 
		$this->assertEquals($department, $semester->getDepartment());
		
	}
	
	// Check whether the setSemesterStartDate function is working correctly
	public function testSetSemesterStartDate(){
		
		// New datetime variable 
		$today = new DateTime("now");
		
		// new entity
		$semester = new Semester();
		
		// Use the setSemesterStartDate method 
		$semester->setSemesterStartDate($today);
		
		// Assert the result 
		$this->assertEquals($today, $semester->getSemesterStartDate());
		
	}
	
	// Check whether the setSemesterEndDate function is working correctly
	public function testSetSemesterEndDate(){
		
		// New datetime variable 
		$today = new DateTime("now");
		
		// new entity
		$semester = new Semester();
		
		// Use the setSemesterEndDate method 
		$semester->setSemesterEndDate($today);
		
		// Assert the result 
		$this->assertEquals($today, $semester->getSemesterEndDate());
		
	}

	
}

   
