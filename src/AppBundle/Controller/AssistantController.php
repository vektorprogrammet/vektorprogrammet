<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Team;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use AppBundle\Repository\Contract\TeamRepositoryInterface;
use AppBundle\Service\Contract\ApplicationAdmissionInterface;
use AppBundle\Service\Contract\AdmissionServiceInterface;
use AppBundle\Service\Contract\FilterServiceInterface;
use AppBundle\Service\Contract\GeoLocationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class AssistantController extends BaseController
{
    private $admissionManager;
    private $admissionService;
    private $entityManager;
    private $departmentRepository;
    private $geoLocation;
    private $filterService;
    private $teamRepository;
    private $admissionPeriodRepository;
    private $eventDispatcher;
    private $formFactory;
    private $kernel;

    /**
     * @param ApplicationAdmissionInterface $admissionManager
     * @param AdmissionServiceInterface $admissionService
     * @param EntityManagerInterface $entityManager
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param GeoLocationInterface $geoLocation
     * @param FilterServiceInterface $filterService
     * @param TeamRepositoryInterface $teamRepository
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @param FormFactoryInterface $formFactory
     * @param KernelInterface $kernel
     */
    public function __construct(
        ApplicationAdmissionInterface $admissionManager,
        AdmissionServiceInterface $admissionService,
        EntityManagerInterface $entityManager,
        DepartmentRepositoryInterface $departmentRepository,
        GeoLocationInterface $geoLocation,
        FilterServiceInterface $filterService,
        TeamRepositoryInterface $teamRepository,
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        EventDispatcherInterface $eventDispatcher,
        FormFactoryInterface $formFactory,
        KernelInterface $kernel
    ) {
        $this->admissionManager = $admissionManager;
        $this->admissionService = $admissionService;
        $this->entityManager = $entityManager;
        $this->departmentRepository = $departmentRepository;
        $this->geoLocation = $geoLocation;
        $this->filterService = $filterService;
        $this->teamRepository = $teamRepository;
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->kernel = $kernel;
    }
    /**
     * @deprecated This resource is only here to serve old urls (e.g. in old emails)
     *
     * @Route("/opptak/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/avdeling/{shortName}",
     *     requirements={"shortName"="(NTNU|NMBU|UiB|UIB|UiO|UIO)"})
     * @Route("/opptak/avdeling/{id}",
     *     requirements={"id"="\d+"},
     *     methods={"GET", "POST"}
     *     )
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function admissionByShortNameAction(Request $request, Department $department)
    {
        return $this->indexAction($request, $department);
    }
    
    /**
     * @Route("/opptak/{city}", name="admission_show_by_city_case_insensitive")
     * @Route("/avdeling/{city}", name="admission_show_specific_department_by_city_case_insensitive")
     *
     * @param Request $request
     * @param $city
     *
     * @return Response
     */
    public function admissionCaseInsensitiveAction(Request $request, $city)
    {
        $department = $this->admissionService->findDepartmentByCity($city);
        if ($department !== null) {
            return $this->indexAction($request, $department);
        } else {
            throw $this->createNotFoundException("Fant ingen avdeling $city.");
        }
    }

    /**
     * @Route("/opptak", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Department|null $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function admissionAction(Request $request, Department $department = null)
    {
        return $this->indexAction($request, $department);
    }

    /**
     * @param Request $request
     * @param Department|null $specificDepartment
     * @param bool $scrollToAdmissionForm
     *
     * @return Response
     */
    public function indexAction(Request $request, Department $specificDepartment = null, $scrollToAdmissionForm = false)
    {
        $pageData = $this->admissionService->prepareAdmissionPageData($specificDepartment);
        $departments = $pageData['departments'];
        $departmentsWithActiveAdmission = $pageData['departmentsWithActiveAdmission'];
        $specificDepartment = $pageData['specificDepartment'];
        $teams = $pageData['teams'];

        $departmentInUrl = $specificDepartment !== null;

        $application = new Application();

        $formViews = array();

        /** @var Department $department */
        foreach ($departments as $department) {
            $form = $this->formFactory->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
                'validation_groups' => array('admission'),
                'departmentId' => $department->getId(),
                'environment' => $this->kernel->getEnvironment(),
            ))->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $scrollToAdmissionForm = true;
                $specificDepartment = $department;
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $submissionResult = $this->admissionService->submitApplication($application, $department);

                if (isset($submissionResult['hasBeenAssistant']) && $submissionResult['hasBeenAssistant']) {
                    return $this->redirectToRoute('admission_existing_user');
                }

                if (isset($submissionResult['error'])) {
                    $this->addFlash('danger', $submissionResult['error']);
                    return $this->redirectToRoute('assistants');
                }

                if (isset($submissionResult['redirectRoute'])) {
                    return $this->redirectToRoute($submissionResult['redirectRoute']);
                }
            }

            $formViews[$department->getCity()] = $form->createView();
        }

        return $this->render('assistant/assistants.html.twig', array(
            'specific_department' => $specificDepartment,
            'department_in_url' => $departmentInUrl,
            'departments' => $departments,
            'departmentsWithActiveAdmission' => $departmentsWithActiveAdmission,
            'teams' => $teams,
            'forms' => $formViews,
            'scroll_to_admission_form' => $scrollToAdmissionForm,
        ));
    }

    /**
     * @Route("/assistenter/opptak/bekreftelse", name="application_confirmation")
     * @return Response
     */
    public function confirmationAction()
    {
        return $this->render('admission/application_confirmation.html.twig');
    }

    /**
     * @Route("/stand/opptak/{shortName}",
     *     name="application_stand_form",
     *     requirements={"shortName"="\w+"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return Response
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function subscribePageAction(Request $request, Department $department)
    {
        if (!$department->activeAdmission()) {
            return $this->indexAction($request, $department);
        }
        $application = new Application();

        $form = $this->formFactory->createNamedBuilder('application_'.$department->getId(), ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
            'environment' => $this->kernel->getEnvironment(),
        ))->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $submissionResult = $this->admissionService->submitApplication($application, $department);

            if (isset($submissionResult['hasBeenAssistant']) && $submissionResult['hasBeenAssistant']) {
                $this->addFlash('warning', $application->getUser()->getEmail().' har vært assistent før. Logg inn med brukeren din for å søke igjen.');
                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            if (isset($submissionResult['error'])) {
                $this->addFlash('danger', $submissionResult['error']);
                return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
            }

            $this->addFlash('success', $application->getUser()->getEmail().' har blitt registrert. Du vil få en e-post med kvittering på søknaden.');
            return $this->redirectToRoute('application_stand_form', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission/application_page.html.twig', [
            'department' => $department,
            'form' => $form->createView()
        ]);
    }
}
