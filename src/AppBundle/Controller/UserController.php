<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Role\Roles;
use AppBundle\Service\Contract\ApplicationManagerInterface;
use AppBundle\Service\Contract\ContentModeManagerInterface;
use AppBundle\Service\Contract\PartnerServiceInterface;
use AppBundle\Service\Contract\RoleManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    private $partnerService;
    private $applicationManager;
    private $contentModeManager;
    private $roleManager;
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $assistantHistoryRepository;
    private $semesterRepository;

    /**
     * @param PartnerServiceInterface $partnerService
     * @param ApplicationManagerInterface $applicationManager
     * @param ContentModeManagerInterface $contentModeManager
     * @param RoleManagerInterface $roleManager
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param SemesterRepositoryInterface $semesterRepository
     */
    public function __construct(
        PartnerServiceInterface $partnerService,
        ApplicationManagerInterface $applicationManager,
        ContentModeManagerInterface $contentModeManager,
        RoleManagerInterface $roleManager,
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        SemesterRepositoryInterface $semesterRepository
    ) {
        $this->partnerService = $partnerService;
        $this->applicationManager = $applicationManager;
        $this->contentModeManager = $contentModeManager;
        $this->roleManager = $roleManager;
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->semesterRepository = $semesterRepository;
    }
    /**
     * @Route("/min-side", name="my_page")
     *
     * @return Response
     */
    public function myPageAction()
    {
        $user = $this->getUser();

        $department = $user->getDepartment();
        $semester = $this->semesterRepository->findOrCreateCurrentSemester();
        $admissionPeriod = $this->admissionPeriodRepository->findOneByDepartmentAndSemester($department, $semester);

        $activeApplication = null;
        if (null !== $admissionPeriod) {
            $activeApplication = $this->applicationRepository->findByUserInAdmissionPeriod($user, $admissionPeriod);
        }

        $applicationStatus = null;
        if (null !== $activeApplication) {
            $applicationStatus = $this->applicationManager->getApplicationStatus($activeApplication);
        }
        $activeAssistantHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesByUser($user);

        return $this->render('my_page/my_page.html.twig', [
            "active_application" => $activeApplication,
            "application_status" => $applicationStatus,
            "active_assistant_histories" => $activeAssistantHistories
        ]);
    }

    /**
     * @Route("/profil/partnere", name="my_partners")
     *
     * @return Response
     */
    public function myPartnerAction()
    {
        if (!$this->getUser()->isActive()) {
            throw $this->createAccessDeniedException();
        }

        $partnerData = $this->partnerService->findPartnersForUser($this->getUser());

        if (empty($partnerData['partnerInformations'])) {
            throw $this->createNotFoundException();
        }

        $semester = $this->semesterRepository->findOrCreateCurrentSemester();
        return $this->render('user/my_partner.html.twig', [
            'partnerInformations' => $partnerData['partnerInformations'],
            'partnerCount' => $partnerData['partnerCount'],
            'semester' => $semester,
        ]);
    }

    /**
     * @Route("profil/mode/{mode}",
     *     name="content_mode",
     *     methods={"POST"}
     *     )
     *
     * @param Request $request
     * @param string $mode
     *
     * @return RedirectResponse
     */
    public function changeContentModeAction(Request $request, string $mode)
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->roleManager->userIsGranted($user, Roles::ADMIN) && !$this->roleManager->userIsInExecutiveBoard($user)) {
            throw $this->createAccessDeniedException();
        }

        if ($mode !== 'read-mode' && $mode !== 'edit-mode') {
            throw new BadRequestHttpException('Invalid mode');
        }

        $isEditMode = $mode === 'edit-mode';

        if ($isEditMode) {
            $this->contentModeManager->changeToEditMode();
        } else {
            $this->contentModeManager->changeToReadMode();
        }

        $this->addFlash($isEditMode ? 'warning' : 'info', $isEditMode ? 'Du er nå i redigeringsmodus' : 'Du er nå i lesemodus');

        return $this->redirect($request->headers->get('referer'));
    }
}
