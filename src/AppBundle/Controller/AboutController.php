<?php

namespace AppBundle\Controller;

class AboutController extends BaseController
{
    public function indexAction()
    {
        return $this->render('about/index.html.twig');
    }
}
