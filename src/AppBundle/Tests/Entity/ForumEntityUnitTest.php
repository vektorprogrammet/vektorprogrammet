<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Forum;
use AppBundle\Entity\Subforum;

class ForumEntityUnitTest extends \PHPUnit_Framework_TestCase {
    
	// Check whether the setName function is working correctly
	public function testSetName(){
		
		// new entity
		$forum= new Forum();
		
		// Use the setName method 
		$forum->setName("Generelt");
		
		// Assert the result 
		$this->assertEquals("Generelt", $forum->getName());
		
	}
	
	// Check whether the setDescription function is working correctly
	public function testSetDescription(){
		
		// new entity
		$forum= new Forum();
		
		// Use the setDescription method 
		$forum->setDescription("Generelt er et forum for alle.");
		
		// Assert the result 
		$this->assertEquals("Generelt er et forum for alle.", $forum->getDescription());
		
	}
	
	// Check whether the setType function is working correctly
	public function testSetType(){
		
		// new entity
		$forum= new Forum();
		
		// Use the setType method 
		$forum->setType("team");
		
		// Assert the result 
		$this->assertEquals("team", $forum->getType());
		
	}
	
	// Check whether the addSubforum function is working correctly
	public function testAddSubforum(){
	
		// new entity
		$forum = new Forum();
		
		$subforum1 = new Subforum();
		
		$subforum1->setName("Subforum1");
		
		// Use the addSubforum method 
		$forum->addSubforum($subforum1);
		
		// Subforums are stored in an array 
		$subforums = $forum->getSubforums();
		
		// Loop through the array and check for matches
		foreach ($subforums as $subforum){
			if ($subforum1 == $subforum){
				// Assert the result
				$this->assertEquals($subforum1, $subforum);	
			}
		}
	}
	
	// Check whether the removeSubforum function is working correctly
	public function testRemoveSubforum(){
	
		// new entity
		$forum = new Forum();
		
		$subforum1 = new Subforum();
		$subforum1->setName("Subforum1");
		$subforum2 = new Subforum();
		$subforum2->setName("Subforum2");
		$subforum3 = new Subforum();
		$subforum3->setName("Subforum3");
		
		// Use the addSubforum method 
		$forum->addSubforum($subforum1);
		$forum->addSubforum($subforum2);
		$forum->addSubforum($subforum3);
		
		// Remove $subforum1
		$forum->removeSubforum($subforum1);
		
		// Subforums are stored in an array 
		$subforums = $forum->getSubforums();
		
		// Loop through the array
		foreach ($subforums as $subforum){
			// Assert the result 
			$this->assertNotEquals($subforum1, $subforum);
		}
		
		
	}
	
}

   
