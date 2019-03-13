<?php
namespace AppBundle\Controller;

use AppBundle\Entity\SurveyNotifier;
use AppBundle\Entity\UserGroup;
use AppBundle\Form\Type\SurveyNotifierType;
use AppBundle\Service\SurveyNotifierManager;
use AppBundle\Service\UserGroupCollectionManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SurveyNotifierController extends BaseController
{


    /**
    * @param Request $request
    * @param SurveyNotifier $surveyNotifier
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function createSurveyNotifierAction(Request $request, SurveyNotifier $surveyNotifier = null)
    {
        $canEdit = true;
        if ($isCreate = $surveyNotifier === null) {
            $surveyNotifier = new SurveyNotifier();
        } else {
            $canEdit = !$surveyNotifier->isActive();
        }


        $form = $this->createForm(SurveyNotifierType::class, $surveyNotifier, array(
            'canEdit' => $canEdit,
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $surveyNotifier->setSenderUser($this->getUser());

            if ($form->get('preview')->isClicked()) {
                return $this->render(
                    'survey/survey_email_notification.html.twig',
                    array(
                        'firstname' => $this->getUser()->getFirstName(),
                        'route' => $this->generateUrl('survey_show', ['id' => $surveyNotifier->getSurvey()->getId()], RouterInterface::ABSOLUTE_URL),
                        'content' => $surveyNotifier->getEmailMessage(),

                    ));
            }

            $this->get(SurveyNotifierManager::class)->initializeSurveyNotifier($surveyNotifier);

            return $this->redirect($this->generateUrl('survey_notifiers'));
        }



        return $this->render('survey/notifier_create.html.twig', array(
            'form' => $form->createView(),
            'surveyNotifier' => $surveyNotifier,
            'isCreate' => $isCreate,
        ));
    }


    public function surveyNotifiersAction()
    {
        $surveyNotifiers =$this->getDoctrine()->getManager()->getRepository(SurveyNotifier::class)->findAll();

        return $this->render('survey/survey_notifiers.html.twig', array(
             'surveyNotifiers' => $surveyNotifiers,
         ));
    }


    public function sendSurveyNotificationsAction(SurveyNotifier $surveyNotifier)
    {
        if ($surveyNotifier->getTimeOfNotification() > new \DateTime() || $surveyNotifier->isAllSent()) {
            throw new AccessDeniedException();
        }
        $isIdentifierCollison = $this->get(SurveyNotifierManager::class)->sendNotifications($surveyNotifier);
        if ($isIdentifierCollison)
        {
            $this->addFlash("danger", "Genererte identifikasjonslenker som ikke er unike, prøv på nytt!");
            $response['cause'] = "Genererte identifikasjonslenker som ikke er unike, prøv på nytt!";
            return $this->redirect($this->generateUrl('survey_notifiers'));
        }
        $this->addFlash("suksess", "Sendt");

        $response['success'] = true;
        return new JsonResponse($response);
    }



    public function deleteSurveyNotifierAction(SurveyNotifier $surveyNotifier)
    {
        if ($surveyNotifier->isActive()) {
            throw new AccessDeniedException();
        }

        $this->getDoctrine()->getManager()->remove($surveyNotifier);
        $response['success'] = true;
        return new JsonResponse($response);
    }
}
