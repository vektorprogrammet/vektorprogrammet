<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PostRepositoryFunctionalTest extends KernelTestCase
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

    // A check whether the post returned by the method is the actual latest post, and a check that the method returns the correct post
    public function testFindLatestPostBySubforum()
    {

//		$posts = $this->em->getRepository('AppBundle:Post')->findLatestPostBySubforum(1);

//		$allPosts= $this->em->getRepository('AppBundle:Post')->findAll();

//		foreach ($posts as $post){

//			foreach ($allPosts as $onePost){
//				// Does oneThread belong to the same subfourm?
//				if ($onePost->getThread()->getSubforum()->getId() == 1) {
//					// Compare the two DateTime variables, both greater than and equal
//					$this->assertGreaterThanOrEqual($onePost->getDateTime(), $post->getDateTime());
//				}
//			}

//			$this->assertEquals(1, $post->getThread()->getSubforum()->getId());

//		}
    }

    // A check whether the forum returned by the method is the correct forum
    public function testFindLatestPostByForum()
    {

//		$posts = $this->em->getRepository('AppBundle:Post')->findLatestPostByForum(1);

//		foreach ($posts as $post){

//			$forums = $post->getThread()->getSubforum()->getForums();

//			foreach ($forums as $forum){

//				if ($forum->getId() == 1 ){
//					$tempForum = $forum;
//				}

//			}

//			$this->assertEquals(1, $tempForum);

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
