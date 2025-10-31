<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionPeriod;
use AppBundle\Entity\Application;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\Semester;
use AppBundle\Service\ApplicationManager;
use AppBundle\Service\ContentModeManager;
use AppBundle\Service\Contract\PartnerServiceInterface;
use AppBundle\Twig\Extension\RoleExtension;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends BaseController
{
    private $partnerService;

    /**
     * @param PartnerServiceInterface $partnerService
     */
    public function __construct(PartnerServiceInterface $partnerService)
    {
        $this->partnerService = $partnerService;
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
        $admissionPeriod = $this->getDoctrine()
            ->getRepository(AdmissionPeriod::class)
            ->findOneByDepartmentAndSemester($department, $semester);

        $activeApplication = null;
        if (null !== $admissionPeriod) {
            $activeApplication = $this->getDoctrine()
                ->getRepository(Application::class)
                ->findByUserInAdmissionPeriod($user, $admissionPeriod);
        }

        $applicationStatus = null;
        if (null !== $activeApplication) {
            $applicationStatus = $this->get(ApplicationManager::class)->getApplicationStatus($activeApplication);
        }
        $activeAssistantHistories = $this->getDoctrine()->getRepository(AssistantHistory::class)->findActiveAssistantHistoriesByUser($user);

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

        $semester = $this->getCurrentSemester();
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
        if (!$this->get(RoleExtension::class)->userCanEditPage()) {
            throw $this->createAccessDeniedException();
        }

        if ($mode !== 'read-mode' && $mode !== 'edit-mode') {
            throw new BadRequestHttpException('Invalid mode');
        }

        $isEditMode = $mode === 'edit-mode';

        if ($isEditMode) {
            $this->get(ContentModeManager::class)->changeToEditMode();
        } else {
            $this->get(ContentModeManager::class)->changeToReadMode();
        }

        $this->addFlash($isEditMode ? 'warning' : 'info', $isEditMode ? 'Du er nå i redigeringsmodus' : 'Du er nå i lesemodus');

        return $this->redirect($request->headers->get('referer'));
    }
}
