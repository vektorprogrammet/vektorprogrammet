<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentAssignment;
use AppBundle\Entity\ParentCourse;
use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;

class ParentAssignmentAdminController extends BaseController
{
    public function showAction(ParentCourse $course)
    {
        return $this->render('parents/parent-assignment-admin-show.html.twig', array(
            'parentsAssigned' => $course->getAssignedParents(),
            'course' => $course,
        ));
    }

    public function createAction(Request $request, ParentCourse $course)
    {
        $parentAssigned = new ParentAssignment();
        $form = $this->createForm(ParentAssignmentType::class, $parentAssigned);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $parentAssigned->setCourse($course);
            $em->persist($parentAssigned);
            $em->flush();

            $this->addFlash("success", "Foreldren er påmeldt.");

            return $this->redirectToRoute('parent_assignment_admin_show', ['id' => $course->getId()]);
        }

        return $this->render('parents/parent-assignment-admin-create.html.twig', array(
            'form' => $form->createView(),
            'courseID' => $course->getId(),
            'courseSpeaker' => $course->getSpeaker(),

        ));
    }
    public function deleteAction(ParentAssignment $parentAssignment)
    {
        $parentCourseID = $parentAssignment->getCourse()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($parentAssignment);
        $em->flush();

        $this->addFlash("success", "\"".$parentAssignment->getName()."\" ble meldt av foreldrekurset");


        return $this->redirectToRoute('parent_assignment_admin_show', ['id' => $parentCourseID]);
    }

};
