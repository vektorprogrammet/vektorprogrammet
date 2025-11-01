<?php

namespace App\Contract;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Interface for Article service operations.
 * This interface defines the contract for article listing and pagination logic.
 */
interface ArticleServiceInterface
{
    /**
     * Get paginated articles for the news page.
     *
     * @param int $page Page number
     * @param int $perPage Articles per page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPaginatedArticles(int $page = 1, int $perPage = 10): \Knp\Component\Pager\Pagination\PaginationInterface;

    /**
     * Get paginated articles filtered by departments.
     *
     * @param array $departments Array of department short names
     * @param int $page Page number
     * @param int $perPage Articles per page
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getPaginatedArticlesByDepartments(array $departments, int $page = 1, int $perPage = 10): \Knp\Component\Pager\Pagination\PaginationInterface;

    /**
     * Get all departments for filtering.
     *
     * @return array
     */
    public function getAllDepartments(): array;

    /**
     * Get latest articles excluding a specific article.
     *
     * @param int $limit Number of articles to return
     * @param int|null $excludeId Article ID to exclude
     * @return array
     */
    public function getLatestArticles(int $limit, ?int $excludeId = null): array;

    /**
     * Get articles for carousel (sticky and latest).
     *
     * @param int $limit Number of articles to return
     * @return array
     */
    public function getCarouselArticles(int $limit = 5): array;

    /**
     * Get latest articles for a specific department.
     *
     * @param int $department Department ID
     * @param int $limit Number of articles to return
     * @return array
     */
    public function getDepartmentArticles(int $department, int $limit = 4): array;
}

