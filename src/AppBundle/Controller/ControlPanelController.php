<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use \DateTime;
use AppBundle\Entity\Forum;
use AppBundle\Form\Type\CreateForumType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ControlPanelController extends Controller {

    public function showAction(){	

		$departments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
		// Return the view to be rendered
		return $this->render('control_panel/index.html.twig', array(
			'departments' => $departments,
		));
			
	}

}
