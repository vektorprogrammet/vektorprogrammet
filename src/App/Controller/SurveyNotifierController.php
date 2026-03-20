<?php

namespace App\Controller;

use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\SurveyNotificationCollection;
use App\Entity\UserGroupCollection;
use App\Form\Type\SurveyNotifierType;
use App\Service\SurveyNotifier;
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
        $isUserGroupCollectionEmpty = empty($this->em->getRepository(UserGroupCollection::class)->findAll());
        if ($isUserGroupCollectionEmpty) {
            $this->addFlash('danger', 'Brukergruppesamling må lages først');

            return $this->redirect($this->generateUrl('survey_notifiers'));
        }

        if ($isCreate = $surveyNotificationCollection === null) {
            $surveyNotificationCollection = new SurveyNotificationCollection();
        }
        $canEdit = !$surveyNotificationCollection->isActive();

        $form = $this->createForm(SurveyNotifierType::class, $surveyNotificationCollection, [
            'canEdit' => $canEdit,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('preview')->isClicked()) {
                $subject = $surveyNotificationCollection->getEmailSubject();
                $emailType = $surveyNotificationCollection->getEmailType();
                $view = 'survey/email_notification.html.twig';
                if ($emailType === 1) {
                    $view = 'survey/default_assistant_survey_notification_email.html.twig';
                } elseif ($emailType === 2) {
                    $view = 'survey/personal_email_notification.html.twig';
                    $subject = 'Hvordan var det på Blussvoll?';
                }

                return $this->render(
                    $view,
                    [
                        'title' => $subject,
                        'firstname' => $this->getUser()->getFirstName(),
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

        if ($surveyNotificationCollection->isAllSent()) {
            $this->addFlash('success', 'Sendt');
            $response['success'] = true;
        } else {
            $this->addFlash('warning', 'Alle ble ikke sendt');
            $response['success'] = false;
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
        $response['success'] = true;

        return new JsonResponse($response);
    }
}
