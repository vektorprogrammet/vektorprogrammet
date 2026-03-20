<?php

namespace App\Content\Infrastructure;

use Symfony\Component\HttpFoundation\RequestStack;

class ContentModeManager
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function isEditMode()
    {
        return $this->requestStack->getSession()->get('edit-mode', false);
    }

    public function changeToEditMode()
    {
        $this->requestStack->getSession()->set('edit-mode', true);
    }

    public function changeToReadMode()
    {
        $this->requestStack->getSession()->set('edit-mode', false);
    }
}
