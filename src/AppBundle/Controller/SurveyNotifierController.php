<?php
namespace AppBundle\Controller;

use AppBundle\Entity\SurveyNotifier;
use AppBundle\Form\Type\SurveyNotifierType;
use AppBundle\Service\SurveyNotifierManager;
use AppBundle\Service\UserGroupCollectionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SurveyNotifierController extends BaseController
{
    public function createSurveyNotifierAction(Request $request, SurveyNotifier $surveyNotifier = null)
    {
        if ($isCreate = $surveyNotifier === null) {
            $surveyNotifier = new SurveyNotifier();
        }

        $form = $this->createForm(SurveyNotifierType::class, $surveyNotifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get(SurveyNotifierManager::class)->initializeSurveyNotifier($surveyNotifier);
        }


            return $this->render('survey/notifier_create.html.twig', array(
            'form' => $form->createView(),
            'isCreate' => $isCreate,
            'surveyNotifier' => $surveyNotifier,
        ));
    }
}
