<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Application;
use AppBundle\Entity\Semester;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SchoolAllocationMainController extends Controller
{
    public function showAction(Request $request, $departmentId = null)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (null === $departmentId) {
            $departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
        }
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($departmentId);
        $applications = $this->getDoctrine()->getRepository('AppBundle:Application')->findAllAllocatableApplicationsBySemester($currentSemester);

        return $this->render('school_admin/school_allocate_main.html.twig', array(
            'applications' => $applications,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isAttending', ChoiceType::class, array(
            'choices' => array(
                'Maybe' => null,
                'Yes' => true,
                'No' => false,
            ),
        ));
    }
}


