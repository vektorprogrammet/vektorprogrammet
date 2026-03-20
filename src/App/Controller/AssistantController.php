<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Application;
use App\Entity\Department;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\Repository\TeamRepository;
use App\Event\ApplicationCreatedEvent;
use App\Form\Type\ApplicationType;
use App\Service\ApplicationAdmission;
use App\Support\FilterService;
use App\Support\Infrastructure\GeoLocation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class AssistantController extends BaseController
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly TeamRepository $teamRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly ApplicationAdmission $applicationAdmission,
        private readonly GeoLocation $geoLocation,
        private readonly FilterService $filterService,
        private readonly FormFactoryInterface $formFactory,
        private readonly KernelInterface $kernel,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EntityManagerInterface $em,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @deprecated This resource is only here to serve old urls (e.g. in old emails)
     *
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/opptak/{shortName}', requirements: ['shortName' => '(NTNU|NMBU|UiB|UIB|UiO|UIO)'])]
    #[Route('/avdeling/{shortName}', requirements: ['shortName' => '(NTNU|NMBU|UiB|UIB|UiO|UIO)'])]
    #[Route('/opptak/avdeling/{id}', requirements: ['id' => "\d+"], methods: ['GET', 'POST'])]
    public function admissionByShortNameAction(Request $request, Department $department)
    {
        return $this->indexAction($request, $department);
    }

    /**
     * @return Response
     */
    #[Route('/opptak/{city}', name: 'admission_show_by_city_case_insensitive')]
    #[Route('/avdeling/{city}', name: 'admission_show_specific_department_by_city_case_insensitive')]
    public function admissionCaseInsensitiveAction(Request $request, $city)
    {
        $city = str_replace(['æ', 'ø', 'å'], ['Æ', 'Ø', 'Å'], $city); // Make sqlite happy
        $department = $this->departmentRepo->findOneByCityCaseInsensitive($city);
        if ($department !== null) {
            return $this->indexAction($request, $department);
        }
        throw $this->createNotFoundException("Fant ingen avdeling $city.");
    }

    /**
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/opptak', methods: ['GET', 'POST'])]
    public function admissionAction(Request $request, ?Department $department = null)
    {
        return $this->indexAction($request, $department);
    }

    /**
     * @param bool $scrollToAdmissionForm
     *
     * @return Response
     */
    #[Route('/studenter', name: 'students', methods: ['GET'])]
    #[Route('/assistenter/{id}', name: 'assistants', defaults: ['id' => null], requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function indexAction(Request $request, ?Department $specificDepartment = null, $scrollToAdmissionForm = false)
    {
        $admissionManager = $this->applicationAdmission;

        $departments = $this->departmentRepo->findActive();
        $departments = $this->geoLocation->sortDepartmentsByDistanceFromClient($departments);
        $departmentsWithActiveAdmission = $this->filterService->filterDepartmentsByActiveAdmission($departments, true);

        $departmentInUrl = $specificDepartment !== null;
        if (!$departmentInUrl) {
            $specificDepartment = $departments[0];
        }

        $teams = $this->teamRepo->findByOpenApplicationAndDepartment($specificDepartment);

        $application = new Application();

        $formViews = [];

        /** @var Department $department */
        foreach ($departments as $department) {
            $form = $this->formFactory->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, [
                'validation_groups' => ['admission'],
                'departmentId' => $department->getId(),
                'environment' => $this->kernel->getEnvironment(),
            ])->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $scrollToAdmissionForm = true;
                $specificDepartment = $department;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $admissionManager->setCorrectUser($application);

                if ($application->getUser()->hasBeenAssistant()) {
                    return $this->redirectToRoute('admission_existing_user');
                }

                $admissionPeriod = $this->admissionPeriodRepo->findOneWithActiveAdmissionByDepartment($department);

                // If no active admission period is found
                if (!$admissionPeriod) {
                    $this->addFlash('danger', $department.' sitt opptak er dessverre stengt.');

                    return $this->redirectToRoute('assistants');
                }
                $application->setAdmissionPeriod($admissionPeriod);
                $this->em->persist($application);
                $this->em->flush();

                $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

                return $this->redirectToRoute('application_confirmation');
            }

            $formViews[$department->getCity()] = $form->createView();
        }

        return $this->render('assistant/assistants.html.twig', [
            'specific_department' => $specificDepartment,
            'department_in_url' => $departmentInUrl,
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/assistenter/opptak/bekreftelse', name: 'application_confirmation')]
    public function confirmationAction()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }

    /**
     * @return Response
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    #[Route('/stand/opptak/{shortName}', name: 'application_stand_form', requirements: ['shortName' => "\w+"])]
    public function subscribePageAction(Request $request, Department $department)
    {
        if (!$department->activeAdmission()) {
            return $this->indexAction($request, $department);
        }
        $admissionManager = $this->applicationAdmission;
        $application = new Application();

        $form = $this->formFactory->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, [
            'validation_groups' => ['admission'],
            'departmentId' => $department->getId(),
            'environment' => $this->kernel->getEnvironment(),
        ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                $this->addFlash('warning', $application->getUser()->getEmail().' har vært assistent før. Logg inn med brukeren din for å søke igjen.');

                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            $admissionPeriod = $this->admissionPeriodRepo->findOneWithActiveAdmissionByDepartment($department);
            $application->setAdmissionPeriod($admissionPeriod);
            $this->em->persist($application);
            $this->em->flush();

            $this->eventDispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);

            $this->addFlash('success', $application->getUser()->getEmail().' har blitt registrert. Du vil få en e-post med kvittering på søknaden.');

            return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission/application_page.html.twig', [
            'department' => $department,
            'form' => $form->createView(),
        ]);
    }
}
