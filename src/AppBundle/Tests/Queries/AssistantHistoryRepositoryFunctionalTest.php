<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssistantHistoryRepositoryFunctionalTest extends KernelTestCase
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

    // Checking whether the method returns the active assistant histories for a given user, but also given the $today variable
    //	public function testFindActiveAssistantHistoriesByUser(){

    //		$assistanthistories = $this->em->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser(1);

    //		$today = new \DateTime('now');

    //		foreach ($assistanthistories as $ah){
    //			$this->assertGreaterThan($ah->getSemester()->getSemesterStartDate(), $today);
    //			$this->assertGreaterThan($today, $ah->getSemester()->getSemesterEndDate());
    //			$this->assertEquals(1, $ah->getUser()->getId());
    //		}

    //	}

    // Checking whether the method returns the active assistant histories for a given school, but also given the $today variable
    //	public function testFindActiveAssistantHistoriesBySchool(){

    //		$assistanthistories = $this->em->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool(1);

    //		$today = new \DateTime('now');

    //		foreach ($assistanthistories as $ah){
    //			$this->assertGreaterThan($ah->getSemester()->getSemesterStartDate(), $today);
    //			$this->assertGreaterThan($today, $ah->getSemester()->getSemesterEndDate());
    //			$this->assertEquals(1, $ah->getSchool()->getId());
    //		}

    //	}

    // Checking whether the method returns all the active assistant histories
    //	public function testFindAllActiveAssistantHistories(){

    //		$assistanthistories = $this->em->getRepository('AppBundle:AssistantHistory')->findAllActiveAssistantHistories();

    //		$today = new \DateTime('now');

    //		foreach ($assistanthistories as $ah){
    //			$this->assertGreaterThan($ah->getSemester()->getSemesterStartDate(), $today);
    //			$this->assertGreaterThan($today, $ah->getSemester()->getSemesterEndDate());
    //		}

    //	}

    // Checking whether the method returns all the inactive assistant histories for a given school ID
    public function testFindInactiveAssistantHistoriesBySchool()
    {

//		$assistanthistories = $this->em->getRepository('AppBundle:AssistantHistory')->findInactiveAssistantHistoriesBySchool(1);

//		$today = new \DateTime('now');

//		foreach ($assistanthistories as $ah){
//			$this->assertGreaterThan($today, $ah->getSemester()->getSemesterStartDate());
//			$this->assertGreaterThan($ah->getSemester()->getSemesterEndDate(), $today);
//			$this->assertEquals(1, $ah->getSchool()->getId());
//		}
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
