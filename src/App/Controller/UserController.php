<?php

namespace App\Controller;

use App\Support\Controller\BaseController;
use App\Entity\Repository\AdmissionPeriodRepository;
use App\Entity\Repository\ApplicationRepository;
use App\Entity\Repository\AssistantHistoryRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Service\ApplicationManager;
use App\Service\ContentModeManager;
use App\Support\Twig\RoleExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends BaseController
{
    public function __construct(
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly ApplicationRepository $applicationRepo,
        private readonly AssistantHistoryRepository $assistantHistoryRepo,
        private readonly ApplicationManager $applicationManager,
        private readonly RoleExtension $roleExtension,
        private readonly ContentModeManager $contentModeManager,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/min-side', name: 'my_page')]
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
            'active_application' => $activeApplication,
            'application_status' => $applicationStatus,
            'active_assistant_histories' => $activeAssistantHistories,
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/profil/partnere', name: 'my_partners')]
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
                if ($activeHistory->activeInGroup(1) && $sh->activeInGroup(1)
                    || $activeHistory->activeInGroup(2) && $sh->activeInGroup(2)) {
                    $partners[] = $sh;
                    ++$partnerCount;
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
     * @return RedirectResponse
     */
    #[Route('profil/mode/{mode}', name: 'content_mode', methods: ['POST'])]
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
