<?php

namespace App\Service;

use App\Models\Article;
use App\Repository\Contract\ArticleRepositoryInterface;
use App\Service\Contract\SlugMakerInterface;

/**
 * Service for generating unique slugs for articles.
 */
class SlugMaker implements SlugMakerInterface
{
    private ArticleRepositoryInterface $articleRepository;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     */
    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlugFor(Article $article): string
    {
        $slugs = $this->articleRepository->findSlugs();

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->replaceCharacters($article->title))));
        $i = 2;
        while (array_search($slug, $slugs) !== false) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->replaceCharacters($article->title)))) . '-' . $i;
            $i++;
        }

        $article->slug = $slug;
        return $slug;
    }

    /**
     * Replace special characters with ASCII equivalents.
     *
     * @param string $string
     * @return string
     */
    private function replaceCharacters(string $string): string
    {
        $a = array('Æ', 'Ø', 'Å', 'æ', 'ø', 'å', '&shy;', '-', '!', ',', '.');
        $b = array('AE', 'O', 'A', 'ae', 'o', 'a', '', '', '', '', '', '');
        return str_replace($a, $b, $string);
    }
}
