<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SubstituteRepositoryFunctionalTest extends KernelTestCase
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

    public function testFindSubstitutes()
    {
        //        // Get the substitutes for department with id 1 and semester with id 1
//        $substitutes = $this->em->getRepository('AppBundle:Substitute')->findSubstitutes(1,1);
//
//        // Assert that the number of returned substitutes are correct (5 is the number of subsistute fixtures)
//        $this->assertGreaterThanOrEqual(5, count($substitutes));
//
//        // Assert that the department and semester are correct
//        foreach($substitutes as $substitute) {
//            $this->assertEquals(1, $substitute->getFieldOfStudy()->getDepartment()->getId());
//            $this->assertEquals(1, $substitute->getSemester()->getId());
//        }
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
