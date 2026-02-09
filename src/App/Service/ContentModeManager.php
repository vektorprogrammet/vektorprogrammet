<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class ContentModeManager
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
