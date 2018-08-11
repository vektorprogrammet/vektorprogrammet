<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class ContentModeManager
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function isEditMode()
    {
        return $this->session->get('edit-mode', false);
    }

    public function changeToEditMode()
    {
        $this->session->set('edit-mode', true);
    }

    public function changeToReadMode()
    {
        $this->session->set('edit-mode', false);
    }
}
