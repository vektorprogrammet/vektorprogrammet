<?php

namespace App\Repository\Eloquent;

use App\Models\StaticContent;
use App\Repository\Contract\StaticContentRepositoryInterface;

/**
 * Eloquent implementation of StaticContentRepositoryInterface.
 */
class StaticContentRepository implements StaticContentRepositoryInterface
{
    /**
     * Find static content by HTML ID.
     *
     * @param string $htmlId
     * @return StaticContent|null
     */
    public function findOneByHtmlId(string $htmlId): ?StaticContent
    {
        return StaticContent::where('html_id', $htmlId)->first();
    }
}

