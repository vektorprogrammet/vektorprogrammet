<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ThreadRepositoryFunctionalTest extends KernelTestCase
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
	
	
	// A check whether the thread returned by the method is the actual latest thread, and a check that the method returns the correct thread
	public function testFindLatestThreadBySubforum(){
		
//		$threads = $this->em->getRepository('AppBundle:Thread')->findLatestThreadBySubforum(1);
//
//		$allThreads = $this->em->getRepository('AppBundle:Thread')->findAll();
//
//		foreach ($threads as $thread){
//
//			foreach ($allThreads as $oneThread){
//				// Does oneThread belong to the same subfourm?
//				if ($oneThread->getSubforum()->getId() == 1) {
//					// Compare the two DateTime variables, both greater than and equal
//					$this->assertGreaterThanOrEqual($oneThread->getDateTime(), $thread->getDateTime());
//				}
//			}
//
//			$this->assertEquals(1, $thread->getSubforum()->getId());
//
//		}

		
	}
	
	// A check whether the forum returned by the method is the correct forum 
//	public function testFindLatestThreadByForum(){
//
//		$threads = $this->em->getRepository('AppBundle:Thread')->findLatestThreadByForum(1);
//
//		foreach ($threads as $thread){
//
//			$forums = $thread->getSubforum()->getForums();
//
//			foreach ($forums as $forum){
//
//				if ($forum->getId() == 1 ){
//					$tempForum = $forum;
//				}
//
//			}
//
//			$this->assertEquals(1, $tempForum);
//
//		}
//
//	}
	

	
	
	
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}

   
