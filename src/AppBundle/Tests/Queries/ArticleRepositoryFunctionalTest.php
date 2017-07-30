<?php

namespace AppBundle\Tests\Queries;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleRepositoryFunctionalTest extends KernelTestCase
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

    //    public function testFindLatestArticles()
    //    {
    //        $articles = $this->em->getRepository('AppBundle:Article')->findLatestArticles(2);

    //        // Assert that the number of returned articles are correct
    //        $this->assertEquals(2, count($articles));

    //        // Compare the dates
    //        $date1 = $articles[0]->getCreated();
    //        $date2 = $articles[1]->getCreated();
    //        $this->assertGreaterThanOrEqual($date1, $date2);
    //    }

    //    public function testFindLatestArticlesByDepartment()
    //    {
    //        $articles = $this->em->getRepository('AppBundle:Article')->findLatestArticlesByDepartment(1,2);

    //        // Assert that the number of returned articles are correct
    //        $this->assertEquals(2, count($articles));

    //        // Compare the dates
    //        $date1 = $articles[0]->getCreated();
    //        $date2 = $articles[1]->getCreated();
    //        $this->assertGreaterThanOrEqual($date1, $date2);

    //        // Assert that the department is correct
    //        foreach($articles as $article) {
    //            foreach($article->getDepartments() as $department) {
    //                $this->assertEquals(1, $department->getId());
    //            }
    //        }
    //    }

    //    public function testFindStickyAndLatestArticles()
    //    {
    //        $articles = $this->em->getRepository('AppBundle:Article')->findStickyAndLatestArticles(1);

    //        // Assert that the number of returned articles are correct
    //        $this->assertEquals(1, count($articles));

    //        // Assert if the article is sticky
    //        $this->assertEquals(true, $articles[0]->getSticky());
    //    }

    public function testFindAllArticles()
    {
        //        // This repository method returns a query builder (used by a paginator).
//        $articles = $this->em->getRepository('AppBundle:Article')->findAllArticles()->getQuery()->getResult();

//        // Assert that the number of returned articles are correct (8 is the number of article fixtures)
//        $this->assertGreaterThanOrEqual(8, count($articles));
    }

    //    public function testAllArticlesByDepartments()
    //    {
    //        // This repository method returns a query builder (used by a paginator).
    //        $articles = $this->em->getRepository('AppBundle:Article')->findAllArticlesByDepartments(['ntnu'])->getQuery()->getResult();

    //        // Assert that the number of returned articles are correct (5 is the number of article fixtures connected to ntnu)
    //        $this->assertEquals(5, count($articles));

    //        // Assert that the department is correct
    //        foreach($articles as $article) {
    //            foreach($article->getDepartments() as $department) {
    //                $this->assertEquals(1, $department->getId());
    //            }
    //        }
    //    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
