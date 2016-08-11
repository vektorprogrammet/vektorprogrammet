<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SchoolRepositoryFunctionalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /* Removed this method 
    
    // A test to check whether the method returns an array of schools given the ID
    public function testSchoolByName(){
        
        $schools = $this->em->getRepository('AppBundle:School')->schoolByName(1);
        
        foreach ($schools as $s){
            $this->assertEquals(1, $s->getDepartment()->getId());
        }
        
    }
    */

    // A test to check whether the method returns an array of schools given by the ID integer of a specific department 
    public function testFindSchoolsByDepartment()
    {

//		$schools = $this->em->getRepository('AppBundle:School')->findSchoolsByDepartment(1);
//
//		foreach ($schools as $s){
//
//			$departments = $s->getDepartments();
//
//			foreach ($departments as $d){
//				$this->assertEquals(1, $d->getId());
//			}
//		}
    }

    // A test to check whether the method returns the correct number of schools in the database 
    public function testGetNumberOfSchools()
    {

//		$schools = $this->em->getRepository('AppBundle:School')->getNumberOfSchools();
//
//		// Insert the number of schools in the database here
//		$this->assertEquals("30", $schools);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
