<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ParentCourse;

class ParentsController extends BaseController
{
    public function showAction()
    {
        $parentCourses = $this->getDoctrine()->getRepository('AppBundle:ParentCourse')->findAllParentCoursesOrderedByDate();
        return $this->render('/parents/parents.html.twig', array(
            'parentCourses' => $parentCourses,
        ));
    }
}
