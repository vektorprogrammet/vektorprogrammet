<?php

namespace App\Shared\Controller;

use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Entity\Semester;
use App\Shared\Form\CreateSemesterType;
use App\Shared\Repository\SemesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SemesterController extends BaseController
{
    public function __construct(
        private readonly SemesterRepository $semesterRepo,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route(name: 'semester_show', path: '/kontrollpanel/semesteradmin')]
    public function showAction()
    {
        $semesters = $this->semesterRepo->findAllOrderedByAge();

        return $this->render('semester_admin/index.html.twig', [
            'semesters' => $semesters,
        ]);
    }

    /**
     * @return RedirectResponse|Response
     *
     * @throws NonUniqueResultException
     */
    #[Route(name: 'semester_create', path: '/kontrollpanel/semesteradmin/opprett')]
    public function createSemesterAction(Request $request)
    {
        $semester = new Semester();

        // Create the form
        $form = $this->createForm(CreateSemesterType::class, $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            // Check if semester already exists
            $existingSemester = $this->semesterRepo
                ->findByTimeAndYear($semester->getSemesterTime(), $semester->getYear());

            // Return to semester page if semester already exists
            if ($existingSemester !== null) {
                $this->addFlash('warning', "Semesteret $existingSemester finnes allerede");

                return $this->redirectToRoute('semester_create');
            }

            $this->em->persist($semester);
            $this->em->flush();

            return $this->redirectToRoute('semester_show');
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deleteAction(Semester $semester)
    {
        $this->em->remove($semester);
        $this->em->flush();

        return new JsonResponse(['success' => true]);
    }
}
