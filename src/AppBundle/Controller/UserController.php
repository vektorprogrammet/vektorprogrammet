<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
use AppBundle\Repository\Contract\AdmissionPeriodRepositoryInterface;
use AppBundle\Repository\Contract\ApplicationRepositoryInterface;
use AppBundle\Repository\Contract\AssistantHistoryRepositoryInterface;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\ApplicationManagerInterface;
use AppBundle\Service\Contract\ContentModeManagerInterface;
use AppBundle\Twig\Extension\RoleExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    private $admissionPeriodRepository;
    private $applicationRepository;
    private $assistantHistoryRepository;
    private $semesterRepository;
    private $applicationManager;
    private $roleExtension;
    private $contentModeManager;

    /**
     * @param AdmissionPeriodRepositoryInterface $admissionPeriodRepository
     * @param ApplicationRepositoryInterface $applicationRepository
     * @param AssistantHistoryRepositoryInterface $assistantHistoryRepository
     * @param SemesterRepositoryInterface $semesterRepository
     * @param ApplicationManagerInterface $applicationManager
     * @param RoleExtension $roleExtension
     * @param ContentModeManagerInterface $contentModeManager
     */
    public function __construct(
        AdmissionPeriodRepositoryInterface $admissionPeriodRepository,
        ApplicationRepositoryInterface $applicationRepository,
        AssistantHistoryRepositoryInterface $assistantHistoryRepository,
        SemesterRepositoryInterface $semesterRepository,
        ApplicationManagerInterface $applicationManager,
        RoleExtension $roleExtension,
        ContentModeManagerInterface $contentModeManager
    ) {
        $this->admissionPeriodRepository = $admissionPeriodRepository;
        $this->applicationRepository = $applicationRepository;
        $this->assistantHistoryRepository = $assistantHistoryRepository;
        $this->semesterRepository = $semesterRepository;
        $this->applicationManager = $applicationManager;
        $this->roleExtension = $roleExtension;
        $this->contentModeManager = $contentModeManager;
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
        $activeAssistantHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesByUser($this->getUser());
        if (empty($activeAssistantHistories)) {
            throw $this->createNotFoundException();
        }

        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->assistantHistoryRepository->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
            $partners = [];

            foreach ($schoolHistories as $sh) {
                if ($sh->getUser() === $this->getUser()) {
                    continue;
                }
                if ($sh->getDay() !== $activeHistory->getDay()) {
                    continue;
                }
                if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1) ||
                    $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                    $partners[] = $sh;
                    $partnerCount++;
                }
            }
            $partnerInformations[] = [
                'school' => $activeHistory->getSchool(),
                'assistantHistory' => $activeHistory,
                'partners' => $partners,
            ];
        }

        $semester = $this->semesterRepository->findOrCreateCurrentSemester();
        return $this->render('user/my_partner.html.twig', [
            'partnerInformations' => $partnerInformations,
            'partnerCount' => $partnerCount,
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
        if (!$this->roleExtension->userCanEditPage()) {
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
