<?php

namespace App\Controller;

use App\Entity\AdmissionPeriod;
use App\Entity\Application;
use App\Entity\AssistantHistory;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Entity\Semester;
use App\Service\ApplicationManager;
use App\Service\ContentModeManager;
use App\Twig\Extension\RoleExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    public function __construct(
        private AdmissionPeriodRepository $admissionPeriodRepo,
        private ApplicationRepository $applicationRepo,
        private AssistantHistoryRepository $assistantHistoryRepo,
        private ApplicationManager $applicationManager,
        private RoleExtension $roleExtension,
        private ContentModeManager $contentModeManager,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
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
        $semester = $this->getCurrentSemester();
        $admissionPeriod = $this->admissionPeriodRepo
            ->findOneByDepartmentAndSemester($department, $semester);

        $activeApplication = null;
        if (null !== $admissionPeriod) {
            $activeApplication = $this->applicationRepo
                ->findByUserInAdmissionPeriod($user, $admissionPeriod);
        }

        $applicationStatus = null;
        if (null !== $activeApplication) {
            $applicationStatus = $this->applicationManager->getApplicationStatus($activeApplication);
        }
        $activeAssistantHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesByUser($user);

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
        $activeAssistantHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesByUser($this->getUser());
        if (empty($activeAssistantHistories)) {
            throw $this->createNotFoundException();
        }

        $partnerInformations = [];
        $partnerCount = 0;

        foreach ($activeAssistantHistories as $activeHistory) {
            $schoolHistories = $this->assistantHistoryRepo->findActiveAssistantHistoriesBySchool($activeHistory->getSchool());
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

        $semester = $this->getCurrentSemester();
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
