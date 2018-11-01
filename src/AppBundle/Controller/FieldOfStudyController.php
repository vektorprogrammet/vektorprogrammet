<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FieldOfStudy;
use AppBundle\Form\Type\FieldOfStudyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FieldOfStudyController extends BaseController
{
    public function showAction()
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        $fieldOfStudies = $this->getDoctrine()->getRepository('AppBundle:FieldOfStudy')->findByDepartment($department);

        return $this->render('field_of_study/show_all.html.twig', array(
            'fieldOfStudies' => $fieldOfStudies,
            'department' => $department,
        ));
    }

    public function editAction(Request $request, FieldOfStudy $fieldOfStudy = null)
    {
        $isEdit = true;
        if ($fieldOfStudy === null) {
            $fieldOfStudy = new FieldOfStudy();
            $isEdit = false;
        } else {
            // Check if user is trying to edit FOS from department other than his own
            if ($fieldOfStudy->getDepartment() !== $this->getUser()->getFieldOfStudy()->getDepartment()) {
                throw new AccessDeniedException();
            }
        }
        $form = $this->createForm(FieldOfStudyType::class, $fieldOfStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fieldOfStudy->setDepartment($this->getUser()->getFieldOfStudy()->getDepartment());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($fieldOfStudy);
            $manager->flush();

            return $this->redirectToRoute('show_field_of_studies');
        }

        return $this->render('field_of_study/form.html.twig', array(
            'form' => $form->createView(),
            'isEdit' => $isEdit,
            'fieldOfStudy' => $fieldOfStudy,
        ));
    }
}
