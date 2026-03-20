<?php

namespace App\Support\Twig;

use App\Service\ContentModeManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentModeExtension extends AbstractExtension
{
    public function __construct(private readonly ContentModeManager $contentModeManager)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('is_edit_mode', $this->isEditMode(...)),
        ];
    }

    public function isEditMode()
    {
        return $this->contentModeManager->isEditMode();
    }
}
