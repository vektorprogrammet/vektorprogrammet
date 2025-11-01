<?php

namespace AppBundle\Service;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Service\Contract\ArticleManagementServiceInterface;
use AppBundle\Service\Contract\FileUploaderInterface;
use AppBundle\Service\Contract\LogServiceInterface;
use AppBundle\Service\Contract\SlugMakerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for managing articles and article-related business logic.
 */
class ArticleManagementService implements ArticleManagementServiceInterface
{
    private $entityManager;
    private $slugMaker;
    private $fileUploader;
    private $logService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param SlugMakerInterface $slugMaker
     * @param FileUploaderInterface $fileUploader
     * @param LogServiceInterface $logService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugMakerInterface $slugMaker,
        FileUploaderInterface $fileUploader,
        LogServiceInterface $logService
    ) {
        $this->entityManager = $entityManager;
        $this->slugMaker = $slugMaker;
        $this->fileUploader = $fileUploader;
        $this->logService = $logService;
    }

    /**
     * {@inheritdoc}
     */
    public function createArticle(Article $article, User $author, Request $request): array
    {
        $article->setAuthor($author);
        $this->slugMaker->setSlugFor($article);

        $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
        $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');

        if (!$imageSmall || !$imageLarge) {
            return ['success' => false, 'error' => 'Error uploading images'];
        }

        $article->setImageSmall($imageSmall);
        $article->setImageLarge($imageLarge);

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $this->logService->info("A new article \"{$article->getTitle()}\" by {$article->getAuthor()} has been published");

        return ['success' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function updateArticle(Article $article, User $editor, Request $request): array
    {
        $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
        if ($imageSmall) {
            $article->setImageSmall($imageSmall);
        }

        $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
        if ($imageLarge) {
            $article->setImageLarge($imageLarge);
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $this->logService->info("The article \"{$article->getTitle()}\" was edited by {$editor}");

        return ['success' => true];
    }

    /**
     * {@inheritdoc}
     */
    public function toggleStickyStatus(Article $article): array
    {
        try {
            if ($article->getSticky()) {
                $article->setSticky(false);
                $sticky = false;
            } else {
                $article->setSticky(true);
                $sticky = true;
            }

            $this->entityManager->persist($article);
            $this->entityManager->flush();

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
        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}

