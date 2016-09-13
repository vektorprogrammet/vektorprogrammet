<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Subscriber;
use AppBundle\Form\SubscribeToNewsletterType;
use AppBundle\Form\Type\CreateNewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends Controller
{
    public function createAction(Request $request, Newsletter $newsletter = null)
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();

        if($newsletter == null){
            $newsletter = new Newsletter();
            $newsletter->setDepartment($department);
        }

        $form = $this->createForm(new CreateNewsletterType(), $newsletter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newsletter);
            $manager->flush();

            return $this->redirectToRoute('newsletter_show_all');
        }

        return $this->render('newsletter/create_newsletter.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function showAllNewslettersAction()
    {
        $newsletters = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findAll();
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        
        return $this->render('newsletter/show_all_newsletters.html.twig', array(
            'newsletters' => $newsletters,
            'department' => $department
        ));
    }
    
    public function showSubscribePageAction(Request $request, Newsletter $newsletter){
        $subscriber = new Subscriber();
        
        $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $subscriber->setNewsletter($newsletter);
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($subscriber);
            $manager->flush();

            $this->addFlash('success', $subscriber->getEmail() . ' ble registrert');
            
            return $this->redirectToRoute('newsletter_show_subscribe', array('id' => $newsletter->getId()));
        }
        
        return $this->render('newsletter/subscribe.html.twig', array(
            'newsletter' => $newsletter,
            'form' => $form->createView()
        ));
    }

    public function showSubscribeAction(Department $department)
    {
        $newsletters = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);
        dump($newsletters);

        return $this->render('', array(
            'newsletters' => $newsletters
        ));
    }
    
    public function showSubscribersAction(Newsletter $newsletter)
    {
        return $this->render('newsletter/show_subscribers.html.twig', array(
            'newsletter' => $newsletter
        ));
    }
    
    public function deleteAction(Newsletter $newsletter)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($newsletter);
        $manager->flush();
        
        return $this->redirectToRoute('newsletter_show_all');
    }

    public function adminUnsubscribeAction(Subscriber $subscriber)
    {
        $newsletterId = $subscriber->getNewsletter()->getId();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($subscriber);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_subscribers', array('id' => $newsletterId));

    }
}
