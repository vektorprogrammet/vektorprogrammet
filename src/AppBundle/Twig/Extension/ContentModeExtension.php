<?php


namespace AppBundle\Twig\Extension;

use AppBundle\Service\ContentModeManager;

class ContentModeExtension extends \Twig_Extension
{
    private $contentModeManager;

    public function __construct(ContentModeManager $contentModeManager)
    {
        $this->contentModeManager = $contentModeManager;
    }

    public function getFunctions()
    {
        return array(
        	new \Twig_SimpleFunction('is_edit_mode', [$this, 'isEditMode']),
        );
    }

    public function isEditMode()
    {
        return $this->contentModeManager->isEditMode();
    }
}
