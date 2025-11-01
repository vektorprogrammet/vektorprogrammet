<?php

namespace App\Repository\Contract;

use App\Models\Article;
use Doctrine\ORM\QueryBuilder;

/**
 * Interface for Article repository operations.
 * This interface defines the contract for article data access,
 * making it easier to migrate to Laravel's repository pattern or Eloquent.
 */
interface ArticleRepositoryInterface
{
    /**
     * Find articles ordered descending by date created.
     *
     * @param int|null $limit
     * @param int|null $excludeId Article ID to exclude from results
     * @return Article[]
     */
    public function findLatestArticles($limit = null, $excludeId = null): array;

    /**
     * Find articles for the given department.
     *
     * @param mixed $department Department ID or entity
     * @param int|null $limit
     * @return Article[]
     */
    public function findLatestArticlesByDepartment($department, $limit = null): array;

    /**
     * Find articles ordered by sticky first, then by date.
     * Returns sticky articles first, then latest articles.
     *
     * @param int|null $limit
     * @return Article[]
     */
    public function findStickyAndLatestArticles($limit = null): array;

    /**
     * Find all article slugs.
     *
     * @return string[]
     */
    public function findSlugs(): array;

    /**
     * Find all articles ordered descending by date created.
     * Returns QueryBuilder for use with pagination.
     *
     * @return QueryBuilder
     */
    public function findAllArticles(): QueryBuilder;

    /**
     * Find all published articles ordered descending by date created.
     * Returns QueryBuilder for use with pagination.
     *
     * @return QueryBuilder
     */
    public function findAllPublishedArticles(): QueryBuilder;

    /**
     * Find all articles for the given departments.
     * Also includes articles which belong to no specific department.
     *
     * @param mixed $departments Department IDs or entities
     * @return QueryBuilder
     */
    public function findAllArticlesByDepartments($departments): QueryBuilder;
}

