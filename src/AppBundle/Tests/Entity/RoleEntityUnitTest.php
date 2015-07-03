<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;

class RoleEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	
	// Check whether the setId function is working correctly
	public function testSetId(){
		
		// new entity
		$role = new Role();
		
		// Use the setName method 
		$role->setId(1);
		
		// Assert the result 
		$this->assertEquals(1, $role->getId());
		
	}
	
	// Check whether the setName function is working correctly
	public function testSetName(){
		
		// new entity
		$role = new Role();
		
		// Use the setName method 
		$role->setName("Admin");
		
		// Assert the result 
		$this->assertEquals("Admin", $role->getName());
		
	}
	
	// Check whether the setRole function is working correctly
	public function testSetRole(){
		
		// new entity
		$role = new Role();
		
		// Use the setRole method 
		$role->setRole("ROLE_ADMIN");
		
		// Assert the result 
		$this->assertEquals("ROLE_ADMIN", $role->getRole());
		
	}
	
	// Check whether the setUsers function is working correctly
	public function testSetUsers(){
	
		// new entity
		$role = new Role();
		
		// Dummy entity
		$user1 = new User();
		$user1->setFirstName("Per");
		$user2 = new User();
		$user2->setFirstName("Hans");
		$user3 = new User();
		$user3->setFirstName("Ole");
	
		// Create a array with the users
		$userList = array($user1, $user2, $user3);
		
		// set the array as users of the role 
		$role->setUsers($userList);
		
		// Users are stored in an array 
		$users = $role->getUsers();
		
		// Loop through the array and check for matches
		foreach ($users as $user){
			foreach ($userList as $u){
				if ($u == $user){
					// Assert the result
					$this->assertEquals($u, $user);	
				}
			}
		}
	}
	
	// Check whether the addUser function is working correctly
	public function testAddUser(){
	
		// new entity
		$role = new Role();
		
		// Dummy entity
		$user1 = new User();
		$user1->setFirstName("Per");

		// Use the addUser method 
		$role->addUser($user1);
		
		// Users are stored in an array 
		$users = $role->getUsers();
		
		// Loop through the array and check for matches
		foreach ($users as $user){
			if ($user1 == $user){
				// Assert the result
				$this->assertEquals($user1, $user);	
			}
		}
	}
	
	// Check whether the removeUser function is working correctly
	public function testRemoveUser(){
	
		// new entity
		$role = new Role();
		
		$user1 = new User();
		$user1->setFirstName("Per");
		$user2 = new User();
		$user2->setFirstName("Hans");
		$user3 = new User();
		$user3->setFirstName("Ole");
		
		// Use the addUser method 
		$role->addUser($user1);
		$role->addUser($user2);
		$role->addUser($user3);
		
		// Remove $user1
		$role->removeUser($user1);
		
		// Teams are stored in an array 
		$users = $role->getUsers();
		
		// Loop through the array
		foreach ($users as $user){
			// Assert the result 
			$this->assertNotEquals($user1, $user);
		}
		
		
	}
	
	
}

   
