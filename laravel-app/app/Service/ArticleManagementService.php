<?php

namespace App\Service;

use App\Models\Article;
use App\Models\User;
use App\Service\Contract\ArticleManagementServiceInterface;
use App\Service\Contract\FileUploaderInterface;
use App\Service\Contract\LogServiceInterface;
use App\Service\Contract\SlugMakerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for managing articles and article-related business logic.
 */
class ArticleManagementService implements ArticleManagementServiceInterface
{
    private SlugMakerInterface $slugMaker;
    private FileUploaderInterface $fileUploader;
    private LogServiceInterface $logService;

    /**
     * @param SlugMakerInterface $slugMaker
     * @param FileUploaderInterface $fileUploader
     * @param LogServiceInterface $logService
     */
    public function __construct(
        SlugMakerInterface $slugMaker,
        FileUploaderInterface $fileUploader,
        LogServiceInterface $logService
    ) {
        $this->slugMaker = $slugMaker;
        $this->fileUploader = $fileUploader;
        $this->logService = $logService;
    }

    /**
     * {@inheritdoc}
     */
    public function createArticle(Article $article, User $author, Request $request): array
    {
        $article->author_id = $author->id;
        $this->slugMaker->setSlugFor($article);

        $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
        $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');

        if (!$imageSmall || !$imageLarge) {
            return ['success' => false, 'error' => 'Error uploading images'];
        }

        $article->image_small = $imageSmall;
        $article->image_large = $imageLarge;

        $article->save();

        $this->logService->info("A new article \"{$article->title}\" by {$author} has been published");

        return ['success' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function updateArticle(Article $article, User $editor, Request $request): array
    {
        $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
        if ($imageSmall) {
            $article->image_small = $imageSmall;
        }

        $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
        if ($imageLarge) {
            $article->image_large = $imageLarge;
        }

        $article->save();

        $this->logService->info("The article \"{$article->title}\" was edited by {$editor}");

        return ['success' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function toggleStickyStatus(Article $article): array
    {
        try {
            if ($article->sticky) {
                $article->sticky = false;
                $sticky = false;
            } else {
                $article->sticky = true;
                $sticky = true;
            }

            $article->save();

            return [
                'success' => true,
                'sticky' => $sticky,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => [
                    'code' => $e->getCode(),
                    'cause' => 'Det oppstod en feil.',
                ],
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteArticle(Article $article): void
    {
        $article->delete();
    }
}

