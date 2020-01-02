<?php

namespace AppBundle\Controller;

class ParentsController extends BaseController
{
    public function indexAction()
    {
        return $this->render('/parents/parents.html.twig');
    }
}
