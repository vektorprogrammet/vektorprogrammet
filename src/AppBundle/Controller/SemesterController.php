<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Form\Type\CreateSemesterType;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\SemesterManagementServiceInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SemesterController extends BaseController
{
    private $semesterRepository;
    private $semesterManagementService;

    /**
     * @param SemesterRepositoryInterface $semesterRepository
     * @param SemesterManagementServiceInterface $semesterManagementService
     */
    public function __construct(
        SemesterRepositoryInterface $semesterRepository,
        SemesterManagementServiceInterface $semesterManagementService
    ) {
        $this->semesterRepository = $semesterRepository;
        $this->semesterManagementService = $semesterManagementService;
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
            $result = $this->semesterManagementService->createSemester($semester);

            if (!$result['success']) {
                $existingSemester = $result['existingSemester'];
                $this->addFlash('warning', "Semesteret $existingSemester finnes allerede");
                return $this->redirectToRoute('semester_create');
            }

            return $this->redirectToRoute('semester_show');
        }

        // Render the view
        return $this->render('semester_admin/create_semester.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Semester $semester)
    {
        $this->semesterManagementService->deleteSemester($semester);

        return new JsonResponse(array('success' => true));
    }
}
