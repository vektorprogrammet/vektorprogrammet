<?php

namespace App\Repository\Eloquent;

use App\Models\Article;
use App\Repository\Contract\ArticleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Eloquent implementation of ArticleRepositoryInterface.
 */
class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * Find articles ordered descending by date created.
     *
     * @param int|null $limit
     * @param int|null $excludeId Article ID to exclude from results
     * @return Article[]
     */
    public function findLatestArticles($limit = null, $excludeId = null): array
    {
        $query = Article::query()
            ->where('published', true)
            ->orderBy('created', 'desc');

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Find articles for the given department.
     *
     * @param mixed $department Department ID or entity
     * @param int|null $limit
     * @return Article[]
     */
    public function findLatestArticlesByDepartment($department, $limit = null): array
    {
        $departmentId = is_object($department) && method_exists($department, 'getId')
            ? $department->getId()
            : $department;

        $query = Article::query()
            ->whereHas('departments', function ($q) use ($departmentId) {
                $q->where('id', $departmentId);
            })
            ->where('published', true)
            ->orderBy('created', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Find articles ordered by sticky first, then by date.
     * Returns sticky articles first, then latest articles.
     *
     * @param int|null $limit
     * @return Article[]
     */
    public function findStickyAndLatestArticles($limit = null): array
    {
        // Get articles that are newer than 30 days or sticky
        $date = Carbon::now()->subDays(30);

        $query = Article::query()
            ->where('published', true)
            ->where(function ($q) use ($date) {
                $q->where('created', '>', $date)
                  ->orWhere('sticky', true);
            })
            ->orderBy('sticky', 'desc')
            ->orderBy('created', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()->toArray();
    }

    /**
     * Find all article slugs.
     *
     * @return string[]
     */
    public function findSlugs(): array
    {
        return Article::query()
            ->pluck('slug')
            ->toArray();
    }

    /**
     * Find all articles ordered descending by date created.
     * Returns QueryBuilder for use with pagination.
     *
     * Note: In Laravel, this returns an Eloquent query builder instance.
     *
     * @return Builder
     */
    public function findAllArticles(): Builder
    {
        return Article::query()
            ->orderBy('created', 'desc');
    }

    /**
     * Find all published articles ordered descending by date created.
     * Returns QueryBuilder for use with pagination.
     *
     * Note: In Laravel, this returns an Eloquent query builder instance.
     *
     * @return Builder
     */
    public function findAllPublishedArticles(): Builder
    {
        return Article::query()
            ->where('published', true)
            ->orderBy('created', 'desc');
    }

    /**
     * Find all articles for the given departments.
     * Also includes articles which belong to no specific department.
     *
     * @param mixed $departments Department IDs or entities (array of short names)
     * @return Builder
     */
    public function findAllArticlesByDepartments($departments): Builder
    {
        // Ensure $departments is an array
        if (!is_array($departments)) {
            $departments = [$departments];
        }

        // Normalize to short names if needed
        $shortNames = [];
        foreach ($departments as $dept) {
            if (is_object($dept) && method_exists($dept, 'getShortName')) {
                $shortNames[] = $dept->getShortName();
            } elseif (is_string($dept)) {
                $shortNames[] = $dept;
            }
        }

        return Article::query()
            ->where('published', true)
            ->where(function ($query) use ($shortNames) {
                $query->whereHas('departments', function ($q) use ($shortNames) {
                    $q->whereIn('short_name', $shortNames);
                })
                ->orWhereDoesntHave('departments');
            })
            ->orderBy('created', 'desc');
    }
}

