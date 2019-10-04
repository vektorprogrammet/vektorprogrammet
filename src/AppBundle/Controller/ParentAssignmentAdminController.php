<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentAssignmentItem;

class ParentAssignmentAdminController extends BaseController
{
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parents_assigned = $em->getRepository('AppBundle:ParentAssignment')->findAllParents();

        return $this->render('parents/parent-assignment-admin.html.twig', array(
            'parentsAssigned' => $parents_assigned->getQuery()->getResult()
        ));
    }

    };
