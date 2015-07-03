<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SemesterRepositoryFunctionalTest extends KernelTestCase
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
	
	// A test to check whether the method returns an array of semesters that belong to the given department 
	public function testFindAllSemestersByDepartment(){
		
		$semesters = $this->em->getRepository('AppBundle:Semester')->findAllSemestersByDepartment(1);
		
		foreach ($semesters as $s){
			$this->assertEquals(1, $s->getDepartment()->getId());
		}
		
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

   
