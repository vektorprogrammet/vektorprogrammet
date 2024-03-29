<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Service\SlugMaker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SlugMakerTest extends KernelTestCase
{
    /**
     * @var SlugMaker
     */
    private $slugMaker;

    protected function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->slugMaker = $kernel->getContainer()->get(SlugMaker::class);
    }

    public function testTitleAsSlug()
    {
        $article = new Article();
        $article->setTitle('This is a test title');

        $this->slugMaker->setSlugFor($article);
        self::assertEquals('this-is-a-test-title', $article->getSlug());
    }


    public function testSlugAlreadyInUse()
    {
        $article = new Article();
        $article->setTitle('Test title');

        $this->slugMaker->setSlugFor($article);
        self::assertEquals('test-title-2', $article->getSlug());
    }

    public function testSpecialCharacters()
    {
        $article = new Article();
        $article->setTitle('Test title æøå!"');

        $this->slugMaker->setSlugFor($article);
        self::assertEquals('test-title-aeoa-', $article->getSlug());
    }
}
