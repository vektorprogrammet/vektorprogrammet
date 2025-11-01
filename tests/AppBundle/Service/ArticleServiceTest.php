<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\Department;
use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Service\ArticleService;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\TestCase;

class ArticleServiceTest extends TestCase
{
    /**
     * @var ArticleService
     */
    private $service;

    /**
     * @var ArticleRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $articleRepository;

    /**
     * @var DepartmentRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $departmentRepository;

    /**
     * @var PaginatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $paginator;

    protected function setUp()
    {
        $this->articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $this->departmentRepository = $this->createMock(DepartmentRepositoryInterface::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->service = new ArticleService(
            $this->articleRepository,
            $this->departmentRepository,
            $this->paginator
        );
    }

    public function testGetPaginatedArticles()
    {
        $articles = [new Article(), new Article()];
        $pagination = $this->createMock(PaginationInterface::class);

        $this->articleRepository->expects($this->once())
            ->method('findAllPublishedArticles')
            ->willReturn($articles);

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with($articles, 1, 10)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedArticles();

        $this->assertSame($pagination, $result);
    }

    public function testGetPaginatedArticlesByDepartments()
    {
        $departments = ['NTNU', 'UiO'];
        $articles = [new Article()];
        $pagination = $this->createMock(PaginationInterface::class);

        $this->articleRepository->expects($this->once())
            ->method('findAllArticlesByDepartments')
            ->with($departments)
            ->willReturn($articles);

        $this->paginator->expects($this->once())
            ->method('paginate')
            ->with($articles, 2, 20)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedArticlesByDepartments($departments, 2, 20);

        $this->assertSame($pagination, $result);
    }

    public function testGetAllDepartments()
    {
        $departments = [new Department(), new Department()];

        $this->departmentRepository->expects($this->once())
            ->method('findAllDepartments')
            ->willReturn($departments);

        $result = $this->service->getAllDepartments();

        $this->assertEquals($departments, $result);
    }

    public function testGetLatestArticles()
    {
        $articles = [new Article(), new Article(), new Article()];

        $this->articleRepository->expects($this->once())
            ->method('findLatestArticles')
            ->with(3, null)
            ->willReturn($articles);

        $result = $this->service->getLatestArticles(3);

        $this->assertEquals($articles, $result);
    }

    public function testGetLatestArticlesWithExcludeId()
    {
        $articles = [new Article(), new Article()];

        $this->articleRepository->expects($this->once())
            ->method('findLatestArticles')
            ->with(2, 5)
            ->willReturn($articles);

        $result = $this->service->getLatestArticles(2, 5);

        $this->assertEquals($articles, $result);
    }

    public function testGetCarouselArticles()
    {
        $articles = [new Article()];

        $this->articleRepository->expects($this->once())
            ->method('findStickyAndLatestArticles')
            ->with(5)
            ->willReturn($articles);

        $result = $this->service->getCarouselArticles(5);

        $this->assertEquals($articles, $result);
    }

    public function testGetDepartmentArticles()
    {
        $departmentId = 1;
        $articles = [new Article()];

        $this->articleRepository->expects($this->once())
            ->method('findLatestArticlesByDepartment')
            ->with($departmentId, 4)
            ->willReturn($articles);

        $result = $this->service->getDepartmentArticles($departmentId, 4);

        $this->assertEquals($articles, $result);
    }
}

