<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateSemesterType;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\SemesterValidationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SemesterController extends BaseController
{
    private $semesterRepository;
    private $entityManager;
    private $semesterValidationService;

    /**
     * @param SemesterRepositoryInterface $semesterRepository
     * @param EntityManagerInterface $entityManager
     * @param SemesterValidationServiceInterface $semesterValidationService
     */
    public function __construct(
        SemesterRepositoryInterface $semesterRepository,
        EntityManagerInterface $entityManager,
        SemesterValidationServiceInterface $semesterValidationService
    ) {
        $this->semesterRepository = $semesterRepository;
        $this->entityManager = $entityManager;
        $this->semesterValidationService = $semesterValidationService;
    }
    /**
     * @Route(name="semester_show", path="/kontrollpanel/semesteradmin")
     *
     * @return Response
     */
    public function showAction()
    {
        $semesters = $this->semesterRepository->findAllOrderedByAge();

        return $this->render('semester_admin/index.html.twig', array(
            'semesters' => $semesters,
        ));
    }

    /**
     * @Route(name="semester_create", path="/kontrollpanel/semesteradmin/opprett")
     * @param Request $request
     *
     * @return RedirectResponse|Response
     * @throws NonUniqueResultException
     */
    public function createSemesterAction(Request $request)
    {
        $semester = new Semester();

        // Create the form
        $form = $this->createForm(CreateSemesterType::class, $semester);

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isSubmitted() && $form->isValid()) {
            //Check if semester already exists
            $existingSemester = $this->semesterValidationService->findExistingSemester($semester->getSemesterTime(), $semester->getYear());

            //Return to semester page if semester already exists
            if ($existingSemester !== null) {
                $this->addFlash('warning', "Semesteret $existingSemester finnes allerede");
                return $this->redirectToRoute('semester_create');
            }

            $this->entityManager->persist($semester);
            $this->entityManager->flush();

            return $this->redirectToRoute('semester_show');
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Semester $semester)
    {
        $this->entityManager->remove($semester);
        $this->entityManager->flush();

        return new JsonResponse(array('success' => true));
    }
}
