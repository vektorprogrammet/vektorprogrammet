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
            'is_edit_mode' => new \Twig_Function_Method($this, 'isEditMode'),
        );
    }

    public function isEditMode()
    {
        return $this->contentModeManager->isEditMode();
    }
}
