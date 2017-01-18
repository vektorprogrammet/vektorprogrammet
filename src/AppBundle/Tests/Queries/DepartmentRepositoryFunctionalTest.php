<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DepartmentRepositoryFunctionalTest extends KernelTestCase
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
            ->getManager();
    }

    // A test to check whether the method returns an array of departments
    public function testFindAllDepartments()
    {

//		$departments = $this->em->getRepository('AppBundle:Department')->findAllDepartments();

//		$shortNames = array("NTNU", "HiST", "UiO", "NMBU");

//		foreach ($departments as $d){

//			foreach ($shortNames as $name){
//				if ($name == $d->getShortName()) {
//					$this->assertEquals($name, $d->getShortName());
//					unset($shortNames[$name]);
//				}
//			}

//		}
    }

    // A test to check whether the array return by the the findDepartmentById method is the correct department given a ID value
//	public function testFindDepartmentById(){

//		$departments = $this->em->getRepository('AppBundle:Department')->findDepartmentById(1);

//		foreach ($departments as $department) {

//			$this->assertEquals(1, $department->getId());

//		}

//	}

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
