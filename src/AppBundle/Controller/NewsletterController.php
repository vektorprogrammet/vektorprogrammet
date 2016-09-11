<?php

namespace AppBundle\Controller;

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

            return $this->redirectToRoute('control_panel');
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
    
    public function showSubscribeAction(Request $request, Newsletter $newsletter){
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
}
