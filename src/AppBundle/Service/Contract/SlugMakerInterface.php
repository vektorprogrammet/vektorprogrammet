<?php

namespace AppBundle\Service\Contract;

use AppBundle\Entity\Article;

/**
 * Interface for SlugMaker service.
 * Defines contract for slug creation operations.
 */
interface SlugMakerInterface
{
    /**
     * Set slug for article.
     *
     * @param Article $article
     * @return string
     */
    public function setSlugFor(Article $article): string;
}
