<?php

namespace App\Contract;

use App\Models\Article;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for ArticleManagementService.
 *
 * Handles business logic for article administration, including creation, editing,
 * status management, and deletion.
 */
interface ArticleManagementServiceInterface
{
    /**
     * Create a new article with images and logging.
     *
     * @param Article $article
     * @param User $author
     * @param Request $request
     *
     * @return array{success: bool, error?: string} Returns success status or error message
     */
    public function createArticle(Article $article, User $author, Request $request): array;

    /**
     * Update an existing article with optional image updates and logging.
     *
     * @param Article $article
     * @param User $editor
     * @param Request $request
     *
     * @return array{success: bool} Returns success status
     */
    public function updateArticle(Article $article, User $editor, Request $request): array;

    /**
     * Toggle the sticky status of an article.
     *
     * @param Article $article
     *
     * @return array{success: bool, sticky: bool, error?: array} Returns status and new sticky value, or error info
     */
    public function toggleStickyStatus(Article $article): array;

    /**
     * Delete an article.
     *
     * @param Article $article
     *
     * @return void
     */
    public function deleteArticle(Article $article): void;
}

