<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Thread;
use AppBundle\Entity\Subforum;
use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Role;
use \DateTime;

class ThreadEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	// Check whether the setSubject function is working correctly
	public function testSetSubject(){
		
		// new entity
		$thread = new Thread();
		
		// Use the setSubject method 
		$thread->setSubject("huehue");
		
		// Assert the result 
		$this->assertEquals("huehue", $thread->getSubject());
		
	}
	
	// Check whether the setDatetime function is working correctly
	public function testSetDatetime(){
		
		// new entity
		$thread = new Thread();
		
		// Dummy datetime variable
		$now = new DateTime("now");
		
		// Use the setDatetime method 
		$thread->setDatetime($now);
		
		// Assert the result 
		$this->assertEquals($now, $thread->getDatetime());
		
	}
	
	// Check whether the setText function is working correctly
	public function testSetText(){
		
		// new entity
		$thread = new Thread();
		
		// Use the setText method 
		$thread->setText("hehehe");
		
		// Assert the result 
		$this->assertEquals("hehehe", $thread->getText());
		
	}
	
	// Check whether the setSubforum function is working correctly
	public function testSetSubforum(){
		
		// new entity
		$thread = new Thread();
		
		// dummy entity
		$subforum = new Subforum();
		$subforum->setName("test");
		
		// Use the setSubforum method 
		$thread->setSubforum($subforum);
		
		// Assert the result 
		$this->assertEquals($subforum, $thread->getSubforum());
		
	}
	
	// Check whether the setUser function is working correctly
	public function testSetUser(){
		
		// new entity
		$thread = new Thread();
		
		// dummy entity
		$user = new User();
		$user->setFirstName("hodor");
		
		// Use the setUser method 
		$thread->setUser($user);
		
		// Assert the result 
		$this->assertEquals($user, $thread->getUser());
		
	}
	
	// Check whether the addPost function is working correctly
	public function testAddPost(){
	
		// new entity
		$thread = new Thread();
		
		// New dummy entity 
		$post1 = new Post();
		$post1->setSubject("post1");
		
		// Use the addPost method 
		$thread->addPost($post1);
		
		// Posts is stored in an array 
		$posts = $thread->getPosts();
		
		// Loop through the array and check for matches
		foreach ($posts as $post){
			if ($post1 == $post){
				// Assert the result
				$this->assertEquals($post1, $post);	
			}
		}
	}
	
	// Check whether the removePost function is working correctly
	public function testRemovePost(){
	
		// new entity
		$thread = new Thread();
		
		// New dummy entity 
		$post1 = new Post();
		$post1->setSubject("post1");
		$post2 = new Post();
		$post2->setSubject("post2");
		$post3 = new Post();
		$post3->setSubject("post3");
		
		// Use the addPost method 
		$thread->addPost($post1);
		$thread->addPost($post2);
		$thread->addPost($post3);
		
		// Remove $post1 from department 
		$thread->removePost($post1);
		
		// posts is stored in an array 
		$posts = $thread->getPosts();
		
		// Loop through the array
		foreach ($posts as $post){
			// Assert the result 
			$this->assertNotEquals($post1, $post);
		}
	}

	
	// Check whether the removeRole function is working correctly
	public function testRemoveRole(){
	
		// new entity
		$user = new User();
		
		// New dummy entity 
		$role1 = new Role();
		$role1->setSubject("role1");
		$role2 = new Role();
		$role2->setSubject("role2");
		$role3 = new Role();
		$role3->setSubject("role3");
		
		// Use the addRole method 
		$user->addRole($role1);
		$user->addRole($role2);
		$user->addRole($role3);
		
		// Remove $role1
		$user->removeRole($role1);
		
		// roles is stored in an array 
		$roles = $user->getRoles();
		
		// Loop through the array
		foreach ($roles as $role){
			// Assert the result 
			$this->assertNotEquals($role1, $role);
		}
	}


	
}

   
