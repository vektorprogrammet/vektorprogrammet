<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AssistantController extends Controller
{
    public function indexAction()
    {
        return $this->render('assistant/assistants.html.twig');
    }
}
