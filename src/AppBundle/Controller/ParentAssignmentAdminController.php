<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentAssignment;
use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;

class ParentAssignmentAdminController extends BaseController
{
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parents_assigned = $em->getRepository('AppBundle:ParentAssignment')->findAllParentAssignments();

        return $this->render('parents/parent-assignment-admin.html.twig', array(
            'parentsAssigned' => $parents_assigned
        ));
    }

    public function deleteAction(ParentAssignment $parentAssignment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($parentAssignment);
        $em->flush();

        $this->addFlash("success", "\"".$parentAssignment->getNavn()."\" ble slettet");

        return $this->redirectToRoute('parent_registration_admin_show');
    }

};
