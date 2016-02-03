<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AnniversaryRegistration;
use AppBundle\Form\Type\AnniversaryRegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package AppBundle\Controller
 */
class AnniversaryRegistrationController extends Controller
{
   public function showAction(Request $request){
      $registrationLimit = 50;
      $em = $this->getDoctrine()->getEntityManager();
      $registrations = $em->getRepository('AppBundle:AnniversaryRegistration')->findAll();
      if(sizeof($registrations) >= $registrationLimit){
         return $this->render('anniversary_registration/registrationFull.html.twig');
      }
      $anniversaryRegistration = new AnniversaryRegistration();

      $form = $this->createForm(new AnniversaryRegistrationType(), $anniversaryRegistration);

      $form->handleRequest($request);

      if($form->isValid()){
         $registrations = $em->getRepository('AppBundle:AnniversaryRegistration')->findAll();
         if(sizeof($registrations) < $registrationLimit){
            $em->persist($anniversaryRegistration);
            $em->flush();
            return $this->redirect($this->generateUrl('anniversary_registration_complete'));
         }else{
            return $this->render('anniversary_registration/registrationFull.html.twig');
         }

      }

      return $this->render('anniversary_registration/registration.html.twig', array(
         'form' => $form->createView(),
      ));
   }

   public function completeAction(){
      return $this->render('anniversary_registration/registrationCompleted.html.twig');
   }

   public function adminAction(){
      $em = $this->getDoctrine()->getEntityManager();
      $participants = $em->getRepository('AppBundle:AnniversaryRegistration')->findAll();
      return $this->render('anniversary_registration/admin.html.twig', array('participants' => $participants));
   }

   public function paidAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $repo = $em->getRepository('AppBundle:AnniversaryRegistration');
      $participant = $repo->find($request->get('participantId'));
      $participant->setHasPaid(true);
      $em->persist($participant);
      $em->flush();
      return new JsonResponse(array('success' => true));
   }

   public function removePaymentAction(Request $request){
      $em = $this->getDoctrine()->getEntityManager();
      $repo = $em->getRepository('AppBundle:AnniversaryRegistration');
      $participant = $repo->find($request->get('participantId'));
      $participant->setHasPaid(false);
      $em->persist($participant);
      $em->flush();
      return new JsonResponse(array('success' => true));
   }

}
