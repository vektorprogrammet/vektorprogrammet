<?php


namespace AppBundle\Twig\Extension;

use AppBundle\Service\ContentModeManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentModeExtension extends AbstractExtension
{
    private $contentModeManager;

    public function __construct(ContentModeManager $contentModeManager)
    {
        $this->contentModeManager = $contentModeManager;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('is_edit_mode', [$this, 'isEditMode']),
        );
    }

    public function isEditMode()
    {
        return $this->contentModeManager->isEditMode();
    }
}
