<?php

namespace App\Survey\Controller;

use App\Identity\Infrastructure\Entity\User;
use App\Support\Controller\BaseController;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use App\Organization\Infrastructure\Entity\UserGroupCollection;
use App\Survey\Form\SurveyNotifierType;
use App\Survey\Infrastructure\SurveyNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SurveyNotifierController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SurveyNotifier $surveyNotifier,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/undersokelsevarsel/opprett', name: 'survey_notifier_create', methods: ['GET', 'POST'])]
    #[Route('/kontrollpanel/undersokelsevarsel/rediger/{id}', name: 'survey_notifier_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function createSurveyNotifierAction(Request $request, ?SurveyNotificationCollection $surveyNotificationCollection = null)
    {
        $isUserGroupCollectionEmpty = $this->em->getRepository(UserGroupCollection::class)->findAll() === [];
        if ($isUserGroupCollectionEmpty) {
            $this->addFlash('danger', 'Brukergruppesamling må lages først');

            return $this->redirect($this->generateUrl('survey_notifiers'));
        }

        $isCreate = $surveyNotificationCollection === null;
        if ($isCreate) {
            $surveyNotificationCollection = new SurveyNotificationCollection();
        }
        $canEdit = !$surveyNotificationCollection->isActive();

        $form = $this->createForm(SurveyNotifierType::class, $surveyNotificationCollection, [
            'canEdit' => $canEdit,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $previewButton = $form->get('preview');
            if ($previewButton instanceof \Symfony\Component\Form\ClickableInterface && $previewButton->isClicked()) {
                $subject = $surveyNotificationCollection->getEmailSubject();
                $emailType = $surveyNotificationCollection->getEmailType();
                $view = 'survey/email_notification.html.twig';
                if ($emailType === 1) {
                    $view = 'survey/default_assistant_survey_notification_email.html.twig';
                } elseif ($emailType === 2) {
                    $view = 'survey/personal_email_notification.html.twig';
                    $subject = 'Hvordan var det på Blussvoll?';
                }

                $currentUser = $this->getUser();
                $firstname = $currentUser instanceof User ? $currentUser->getFirstName() : '';

                return $this->render(
                    $view,
                    [
                        'title' => $subject,
                        'firstname' => $firstname,
                        'route' => $this->generateUrl('survey_show', ['id' => $surveyNotificationCollection->getSurvey()->getId()], RouterInterface::ABSOLUTE_URL),
                        'day' => 'Mandag',
                        'mainMessage' => $surveyNotificationCollection->getEmailMessage(),
                        'endMessage' => $surveyNotificationCollection->getEmailEndMessage(),
                        'school' => 'Blussvoll',
                        'fromName' => $surveyNotificationCollection->getEmailFromName(),
                    ]
                );
            }

            $this->surveyNotifier->initializeSurveyNotifier($surveyNotificationCollection);

            return $this->redirect($this->generateUrl('survey_notifiers'));
        }

        return $this->render('survey/notifier_create.html.twig', [
            'form' => $form->createView(),
            'surveyNotificationCollection' => $surveyNotificationCollection,
            'isCreate' => $isCreate,
            'isUserGroupCollectionEmpty' => $isUserGroupCollectionEmpty,
        ]);
    }

    #[Route('/kontrollpanel/undersokelsevarsel', name: 'survey_notifiers', methods: ['GET'])]
    public function surveyNotificationCollectionsAction()
    {
        $surveyNotificationCollections = $this->em->getRepository(SurveyNotificationCollection::class)->findAll();

        return $this->render('survey/notifiers.html.twig', [
            'surveyNotificationCollections' => $surveyNotificationCollections,
        ]);
    }

    #[Route('/kontrollpanel/undersokelsevarsel/send/{id}', name: 'survey_notifier_send', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function sendSurveyNotificationsAction(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->getTimeOfNotification() > new \DateTime() || $surveyNotificationCollection->isAllSent()) {
            throw new AccessDeniedException();
        }
        $this->surveyNotifier->sendNotifications($surveyNotificationCollection);

        $allSent = $surveyNotificationCollection->isAllSent(); // state mutated by sendNotifications()
        if ($allSent) { // @phpstan-ignore if.alwaysFalse
            $this->addFlash('success', 'Sendt');
            $response = ['success' => true];
        } else {
            $this->addFlash('warning', 'Alle ble ikke sendt');
            $response = ['success' => false];
        }

        return new JsonResponse($response);
    }

    #[Route('/kontrollpanel/undersokelsevarsel/slett/{id}', name: 'survey_notifier_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteSurveyNotifierAction(SurveyNotificationCollection $surveyNotificationCollection)
    {
        if ($surveyNotificationCollection->isActive()) {
            throw new AccessDeniedException();
        }

        $this->em->remove($surveyNotificationCollection);
        $response = ['success' => true];

        return new JsonResponse($response);
    }
}
