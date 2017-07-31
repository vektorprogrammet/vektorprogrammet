<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdmissionRepositoryFunctionalTest extends KernelTestCase
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

    //    public function testFindInterviewedApplicants()
    //    {
    //        // Get the interviewed applicants for department with id 1 and semester with id 1
    //        $applications = $this->em->getRepository('Application.php')->findInterviewedApplicants(1,1);

    //        // Assert that the number of returned applications are correct (1 is the number of interviewed application fixtures)
    //        $this->assertGreaterThanOrEqual(1, count($applications));

    //        // Assert that the department and semester are correct, and that the applicants have been interviewed
    //        foreach($applications as $application) {
    //            $this->assertEquals(1, $application->getStatistic()->getSemester()->getDepartment()->getId());
    //            $this->assertEquals(1, $application->getStatistic()->getSemester()->getId());
    //            $this->assertEquals(true, $application->getInterview()->getInterviewed());
    //        }
    //    }

    //    public function testFindAssignedApplicants()
    //    {
    //        // Get the assigned applicants for department with id 1 and semester with id 1
    //        $applications = $this->em->getRepository('Application.php')->findAssignedApplicants(1,1);

    //        // Assert that the number of returned applications are correct (1 is the number of assigned application fixtures)
    //        $this->assertGreaterThanOrEqual(1, count($applications));

    //        // Assert that the department and semester are correct, and that the applicants have been assigned an interview but are not yet interviewed
    //        foreach($applications as $application) {
    //            $this->assertEquals(1, $application->getStatistic()->getSemester()->getDepartment()->getId());
    //            $this->assertEquals(1, $application->getStatistic()->getSemester()->getId());
    //            $this->assertEquals(false, $application->getInterview()->getInterviewed());
    //        }
    //    }

    public function testFindNewApplicants()
    {
        //        // Get the new applicants for department with id 1 and semester with id 1
//        $applications = $this->em->getRepository('Application.php')->findNewApplicants(1,1);

//        // Assert that the number of returned applications are correct (4 is the number of new application fixtures)
//        $this->assertGreaterThanOrEqual(4, count($applications));

//        // Assert that the department and semester are correct, and that the applicants have not been assigned an interview or not yet been interviewed
//        foreach($applications as $application) {
//            $this->assertEquals(1, $application->getStatistic()->getSemester()->getDepartment()->getId());
//            $this->assertEquals(1, $application->getStatistic()->getSemester()->getId());
//            if($application->getInterview()) {
//                $this->assertEquals(false, $application->getInterview()->getInterviewed());
//            } else {
//                $this->assertNull($application->getInterview());
//            }

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
