<?php


namespace AppBundle\Service;

use AppBundle\Service\Contract\ContentModeManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ContentModeManager implements ContentModeManagerInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
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
