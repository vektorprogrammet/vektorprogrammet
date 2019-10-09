<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Service\TeamMembershipService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateSemesterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SemesterController extends Controller
{
    /**
     * @Route(name="semester_show", path="/kontrollpanel/semesteradmin")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction()
    {
        $semesters = $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllOrderedByAge();

        return $this->render('semester_admin/index.html.twig', array(
            'semesters' => $semesters,
        ));
    }

    /**
     * @Route(name="semester_create", path="/kontrollpanel/semesteradmin/opprett")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createSemesterAction(Request $request)
    {
        $semester = new Semester();

        // Create the form
        $form = $this->createForm(CreateSemesterType::class, $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            //Check if semester already exists
            $existingSemester = $this->getDoctrine()->getManager()->getRepository('AppBundle:Semester')
                ->findByTimeAndYear($semester->getSemesterTime(), $semester->getYear());

            //Return to semester page if semester already exists
            if ($existingSemester !== null) {
                $this->addFlash('warning', "Semesteret $existingSemester finnes allerede");
                return $this->redirectToRoute('semester_create');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($semester);
            $em->flush();

            $this->get(TeamMembershipService::class)->updateTeamMemberships();

            return $this->redirectToRoute('semester_show');
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Semester $semester)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($semester);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
