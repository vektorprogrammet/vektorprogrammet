<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Form\Type\ErrorFeedbackType;
use AppBundle\Entity\Feedback;
use AppBundle\Service\SlackMessenger;

class FeedbackController extends BaseController
{
    public function adminSubmitAction(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();

        $form = $this->createForm(FeedBackType::class, $feedback);
        $form->handleRequest($request);

        $returnUri = $request->getUri();
        if ($request->headers->get('referer')) {
            $returnUri = $request->headers->get('referer');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();
            $this->sumbitFeedback($feedback);
            $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");

            return $this->redirect($returnUri); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)// Will also r
        }
        return $this->redirect($returnUri, 307); //307 forwards the POST request, shows previous form submit if not valid
    }
    public function ErrorSubmitAction(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();
        $form = $this->createForm(ErrorFeedBackType::class, $feedback);
        $form->handleRequest($request);

        $returnUri = $request->getUri();
        if ($request->headers->get('referer')) {
            $returnUri = $request->headers->get('referer');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();
            $this->sumbitFeedback($feedback);
            $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");
            return $this->redirect($returnUri); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)// Will also r
        }
        return $this->redirect($returnUri, 307); //307 forwards the POST request, shows previous form submit if not valid
    }
    //Stores feedback
    private function sumbitFeedback(Feedback $feedback)
    {
        $user = $this->getUser();
        //Stores the submitted feedback
        $em = $this->getDoctrine()->getManager();
        if (!$em->isOpen()) {
            $this->getDoctrine()->resetManager();
        }
        $feedback->setUser($user);
        $em->persist($feedback);
        $em->flush();

        //Notifies on slack (NotificationCHannel)
        $messenger = $this->container->get('AppBundle\Service\SlackMessenger');
        $messenger->notify($feedback->getSlackMessageBody());

        $this->addFlash("success", "Tilbakemeldingen har blitt registrert, tusen takk!");
        $feedback = new Feedback;
    }

    //shows form for submitting a new feedback
    public function indexAction(Request $request)
    {
        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback'
        ));
    }
    //Shows a specific feedback
    public function showAction(Request $request, Feedback $feedback)
    {
        return $this->render('feedback_admin/feedback_admin_show.html.twig', array(
            'feedback' => $feedback,
            'title' => $feedback->getTitle(),
        ));
    }

    //Lists all feedbacks
    public function showAllAction(Request $request)
    {
        $paginator  = $this->get('knp_paginator');

        $repository = $this->getDoctrine()->getRepository(Feedback::class);

        //Gets all feedbacks sorted by created_at
        $feedbacks = $repository->findAllSortByNewest();

        $pagination = $paginator->paginate(
            $feedbacks,
            $request->query->get('page', 1),
            15
        );

        return $this->render('feedback_admin/feedback_admin_list.html.twig', array(
            'feedbacks' => $feedbacks,
            'pagination' => $pagination,
            'title' => 'Alle tilbakemeldinger'
        ));
    }
}
