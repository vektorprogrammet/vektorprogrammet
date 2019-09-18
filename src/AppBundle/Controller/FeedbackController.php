<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Entity\Feedback;
use AppBundle\Service\SlackMessenger;

class FeedbackController extends BaseController
{
    //shows form for submitting a new feedback
    public function indexAction(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();
        $form = $this->createForm(FeedBackType::class, $feedback);
        $repository = $this->getDoctrine()->getRepository(Feedback::class)->findAll();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Stores the submitted feedback
            $em = $this->getDoctrine()->getManager();
            $feedback = $form->getData();
            $feedback->setUser($user);
            $em->persist($feedback);
            $em->flush();

            //Notifies on slack (NotificationCHannel) 
            $messenger = $this->container->get('AppBundle\Service\SlackMessenger');
            $messenger->notify($feedback->getSlackMessageBody());

            return $this->redirect($request->getUri()); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback',
            'form' => $form->createView()
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
        $feedbacks = $repository->findAllSort();

        $pagination = $paginator->paginate(
            $feedbacks,
            $request->query->get('page', 1),
            15);

        return $this->render('feedback_admin/feedback_admin_list.html.twig', array(
            'feedbacks' => $feedbacks,
            'pagination' => $pagination,
            'title' => 'Alle tilbakemeldinger'
        ));

    }
}