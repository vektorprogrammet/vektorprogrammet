<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ParentCourseType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentCourse;

class ParentCourseController extends BaseController
{
    public function createAction(Request $request, ParentCourse $parentCourse = null)
    {
        $isCreate = $parentCourse === null;
        if ($isCreate) {
            $parentCourse = new ParentCourse();
            $data = array(
                'speaker' => '',
                'place' => '',
                'link' => '',
                'date' => null,
                'info' => '',
                'label' => 'Opprett');
        } else {
            $data = array(
                'speaker' => $parentCourse->getSpeaker(),
                'place' => $parentCourse->getPlace(),
                'link' => $parentCourse->getLink(),
                'date' => $parentCourse->getDate(),
                'info' => $parentCourse->getInformation(),
                'label' => 'Lagre');
        }

        $form = $this->createForm(ParentCourseType::class, $parentCourse, $data);
        $form->handleRequest($request);


        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parentCourse);
            $em->flush();

            $flash = "Foreldrekurset ble ";
            $flash .= $isCreate ? "opprettet." : "endret.";
            $this->addFlash("success", $flash);

            return $this->redirect($this->generateUrl('parent_course_admin_show'));
        }

        return $this->render('parent_course/parent_course_create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate
        ));
    }

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $parentCourses = $em->getRepository('AppBundle:ParentCourse')->findAllParentCoursesOrderedByDate();

        return $this->render('parent_course/parent_course_show.html.twig', array('parentCourses' => $parentCourses));
    }

    public function deleteAction(ParentCourse $parentCourse)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($parentCourse);
        $em->flush();

        $this->addFlash("success", "Kurset holdt av \"".$parentCourse->getSpeaker()."\" ble slettet");

        return $this->redirectToRoute('parent_course_admin_show');
    }
};
