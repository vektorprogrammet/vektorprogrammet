<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Entity\Feedback;
use AppBundle\Service\SlackMessenger;

class FeedbackController extends BaseController
{
    public function indexAction(Request $request)
    {
        $feedback = new Feedback;
        $user = $this->getUser();
        $messenger = $this->container->get('AppBundle\Service\SlackMessenger');
        $form = $this->createForm(FeedBackType::class, $feedback);

        $feedbacks = 
        $repository = $this->getDoctrine()->getRepository(Feedback::class)->findAll();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $feedback = $form->getData();
            $feedback->setUser($user);
            $em->persist($feedback);
            $em->flush();
            return $this->redirect($request->getUri()); //Makes sure the user cannot submit the same form twice (e.g. by reloading page)


            /* TODO
                - Send Epost?
            */
            $messenger->notify($feedback->getSlackMessageBody());
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback',
            'form' => $form->createView()
        ));
    }
    public function showAction(Request $request, Feedback $feedback)
    {
        return $this->render('feedback_admin/feedback_admin_show.html.twig', array(
            'feedback' => $feedback,
            'title' => $feedback->getTitle(),
        ));
    }

    public function showAllAction(Request $request)
    {
        $paginator  = $this->get('knp_paginator');

        $repository = $this->getDoctrine()->getRepository(Feedback::class);
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