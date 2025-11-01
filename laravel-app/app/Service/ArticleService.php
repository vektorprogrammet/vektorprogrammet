<?php

namespace App\Service;

use App\Repository\Contract\ArticleRepositoryInterface;
use App\Repository\Contract\DepartmentRepositoryInterface;
use App\Service\Contract\ArticleServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Service for article listing and pagination.
 * Extracts business logic from ArticleController.
 */
class ArticleService implements ArticleServiceInterface
{
    private $articleRepository;
    private $departmentRepository;
    private $paginator;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        DepartmentRepositoryInterface $departmentRepository,
        PaginatorInterface $paginator
    ) {
        $this->articleRepository = $articleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->paginator = $paginator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedArticles(int $page = 1, int $perPage = 10): PaginationInterface
    {
        $articles = $this->articleRepository->findAllPublishedArticles();

        return $this->paginator->paginate(
            $articles,
            $page,
            $perPage
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedArticlesByDepartments(array $departments, int $page = 1, int $perPage = 10): PaginationInterface
    {
        $articles = $this->articleRepository->findAllArticlesByDepartments($departments);

        return $this->paginator->paginate(
            $articles,
            $page,
            $perPage
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllDepartments(): array
    {
        return $this->departmentRepository->findAllDepartments();
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestArticles(int $limit, ?int $excludeId = null): array
    {
        return $this->articleRepository->findLatestArticles($limit, $excludeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCarouselArticles(int $limit = 5): array
    {
        return $this->articleRepository->findStickyAndLatestArticles($limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getDepartmentArticles(int $department, int $limit = 4): array
    {
        return $this->articleRepository->findLatestArticlesByDepartment($department, $limit);
    }
}

