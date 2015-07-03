<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MembersController extends Controller {

    public function showAction(){	
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			
			// Get entity manager
			$em = $this->getDoctrine()->getEntityManager();
			
			// Find all users
			$users = $em->getRepository('AppBundle:User')->findAll();
			
			// Find all departments
			$deparments = $em->getRepository('AppBundle:Department')->findAll();
			
			// Return the view to be rendered
			return $this->render('members/members.html.twig', array(
				'users' => $users,
				'departments' => $deparments,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
    }

}
