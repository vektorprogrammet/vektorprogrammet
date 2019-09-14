<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\FeedbackType;
use AppBundle\Entity\Feedback;
use AppBundle\Service\SlackMessenger;

class FeedbackController extends BaseController
{
    private function getMessenger(SlackMessenger $messenger)
    {
        return $messenger;
    }
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
                - Send Epost
                - Send Melding via bot i #IT-General med @channel
            */
        }

        return $this->render('feedback_admin/feedback_admin_index.html.twig', array(
            'title' => 'Feedback',
            'form_1' => $form,
            'feedback' => $feedback,
            'user' => $user,
            'messenger' => $messenger,
            'form' => $form->createView()
        ));
    }
}