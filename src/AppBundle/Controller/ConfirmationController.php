<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ConfirmationController extends BaseController
{
    /**
     * @Route("/bekreftelse", name="confirmation")
     * @Method({"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
