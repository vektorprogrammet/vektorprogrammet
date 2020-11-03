<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ConfirmationController extends BaseController
{
    /**
     * @Route("/bekreftelse", name="confirmation")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function showAction()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
