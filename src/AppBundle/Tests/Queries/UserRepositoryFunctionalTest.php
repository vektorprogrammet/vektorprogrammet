<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryFunctionalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }
	
	// A test to check whether the method finds all users of a given department
	public function testFindAllUsersByDepartment(){
		$users = $this->em->getRepository('AppBundle:User')->findAllUsersByDepartment(1);
		
		foreach ($users as $user ){
			$this->assertEquals(1, $user->getFieldOfStudy()->getDepartment()->getId());
		}
		
	}
	
	// A test to check whether the method finds all users of a given department, and that the user is active 
	public function testFindAllActiveUsersByDepartment(){
		$users = $this->em->getRepository('AppBundle:User')->findAllActiveUsersByDepartment(1);
		
		foreach ($users as $user ){
			$this->assertEquals(1, $user->getFieldOfStudy()->getDepartment()->getId());
			$this->assertEquals(1, $user->getIsActive());
		}
		
	}
	
	/* ????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
	The person who wrote this method should write a test here. 
	
	public function testFindAllUsersByDepartmentAndRoles(){
		$user = $this->em->getRepository('AppBundle:User')->findAllUsersByDepartmentAndRoles(?????);
	}
	*/
	
	// A test to check if the repository finds the correct user given a username
    public function testFindUserByUsername() {
        $user = $this->em->getRepository('AppBundle:User')->findUserByUsername("petjo");
        $this->assertEquals("petjo", $user->getUsername());
    }
	
	// A test to check if the repository finds the correct user given an email
    public function testFindUserByEmail() {
        $user = $this->em->getRepository('AppBundle:User')->findUserByEmail("petter@stud.ntnu.no");
        $this->assertEquals("petter@stud.ntnu.no", $user->getEmail());
    }
	
	// A test to check if the repository finds the correct user given a ID integer 
    public function testFindUserById() {
        $user = $this->em->getRepository('AppBundle:User')->findUserById(1);
        $this->assertEquals(1, $user->getId());
    }
	
	public function testFindUserByNewUserCode(){
		$user = $this->em->getRepository('AppBundle:User')->findUserByNewUserCode(1);
        $this->assertEquals(1, $user->getId());
	}


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}

   
