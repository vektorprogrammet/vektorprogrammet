<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends BaseController
{
    /**
     * @Route("/bekreftelse", name="confirmation", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        return $this->render('confirmation/confirmation.html.twig');
    }
}
