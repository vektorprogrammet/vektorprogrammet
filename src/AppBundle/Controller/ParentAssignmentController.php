<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentCourse;
use AppBundle\Form\Type\ParentAssignmentType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentAssignment;

class ParentAssignmentController extends BaseController
{
    public function createAction(Request $request, ParentCourse $course)
    {
        $parentAssigned = new ParentAssignment();
        $form = $this->createForm(ParentAssignmentType::class, $parentAssigned);
        $form->handleRequest($request);


        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $randomString = uniqid();
            $parentAssignedWithGeneratedKey = $em->getRepository('AppBundle:ParentAssignment')->getParentAssignmentByUniqueKey($randomString);
            while ($parentAssignedWithGeneratedKey != null)
            {
                $randomString = uniqid();
                $parentAssignedWithGeneratedKey = $em->getRepository('AppBundle:ParentAssignment')->getParentAssignmentByUniqueKey($randomString);
            }

            $parentAssigned->setUniqueKey($randomString);
            $parentAssigned->setCourse($course);
            $em->persist($parentAssigned);
            $em->flush();

            $this->addFlash("success", "Din påmelding er registert.");

            return $this->redirect($this->generateUrl('parents'));
        }

        return $this->render('parents/parent-assignment-create.html.twig', array(
            'form' => $form->createView(),
));

    }

    public function deleteAction(ParentAssignment $parentAssignment, int $uniqueKey)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($parentAssignment);
        $em->flush();
        #denne skal brukes til å slette fra mailen som sendes ut til foreldrene!

        return $this->redirectToRoute('parent_assignment_external_delete');
    }

};
