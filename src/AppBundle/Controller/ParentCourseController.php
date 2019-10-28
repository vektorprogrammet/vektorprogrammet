<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ParentCourseType; //This form needs to be made later
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ParentCourse;

class ParentCourseController extends BaseController
{
    public function createAction(Request $request)
    {
        $parentCourse = new ParentCourse();
        $form = $this->createForm(ParentCourseType::class, $parentCourse);
        $form->handleRequest($request);


        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parentCourse);
            $em->flush();

            $this->addFlash("success", "Foreldrekurset er opprettet");

            return $this->redirect($this->generateUrl('parent_course_admin_show'));
        }

        return $this->render('parents/parent-course-admin-show.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();
        $parentCourses = $em->getRepository('AppBundle:ParentCourse')->findAllOrderedByDate(); //Midlertidig funksjon, må lages i repository

        return $this->render('InsertTemplateHere', array('parent_courses' => $parentCourses));
    }

    public function deleteAction(ParentCourse $parentCourse)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($parentCourse);
        $em->flush();

        return $this->redirectToRoute('parent_course_admin_delete');
    }

};
