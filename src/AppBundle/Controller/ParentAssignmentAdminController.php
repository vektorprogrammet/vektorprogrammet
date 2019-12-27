<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentAssignment;
use AppBundle\Entity\ParentCourse;
use Symfony\Component\HttpFoundation\Request;

class ParentAssignmentAdminController extends BaseController
{
    public function showAction(ParentCourse $course)
    {
        return $this->render('parents/parent-assignment-admin.html.twig', array(
            'parentsAssigned' => $course->getAssignedParents()
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
