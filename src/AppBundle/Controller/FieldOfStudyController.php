<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Form\Type\FieldOfStudyType;
use AppBundle\Role\Roles;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FieldOfStudyController extends BaseController
{
    public function showAction(Request $request)
    {
        $departmentId = $request->query->get("departmentId");
        if ($this->isGranted(Roles::ALIAS_ADMIN) && !is_null($departmentId)) {
            $department  = $this->getDoctrine()->getRepository(Department::class)->findDepartmentById($departmentId)[0];
        } else {
            $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        }
        $fieldOfStudies = $this->getDoctrine()->getRepository(FieldOfStudy::class)->findByDepartment($department);

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
            // Check if a non-admin user is trying to edit FOS from department other than his own
            if (!$this->isGranted(Roles::ALIAS_ADMIN) && $fieldOfStudy->getDepartment() !== $this->getUser()->getFieldOfStudy()->getDepartment()) {
                throw new AccessDeniedException();
            }
        }
        $form = $this->createForm(FieldOfStudyType::class, $fieldOfStudy);
        $form->handleRequest($request);

        $departmentId = $request->query->get("departmentId");
        // Use department from request if admin is creating a new field_of_study with id,
        // else use department from current user 
        if (!$this->isGranted(Roles::ALIAS_ADMIN) && !$isEdit && !is_null($departmentId)) {
            $department = $this->getDoctrine()->getRepository(Department::class)->findDepartmentById($departmentId)[0];
            $fieldOfStudy->setDepartment($department);
        } else {
            $department = $this->getUser()->getFieldOfStudy()->getDepartment();
            $fieldOfStudy->setDepartment($department);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($fieldOfStudy);
            $manager->flush();

            return $this->redirectToRoute('show_field_of_studies');
        }

        return $this->render('field_of_study/form.html.twig', array(
            'form' => $form->createView(),
            'isEdit' => $isEdit,
            'fieldOfStudy' => $fieldOfStudy,
            'department' => $department,
        ));
    }
}
