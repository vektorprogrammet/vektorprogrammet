<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\CertificateRequest;

class CertificateController extends Controller
{
    public function showAction()
    {

        // Only ROLE_SUPER_ADMIN can view this 
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Finds all the the certificate requests 
            $certificateRequests = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->findAll();

            return $this->render('certificate/index.html.twig', array(
                'certificateRequests' => $certificateRequests,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }


    public function deleteAction(Request $request)
    {

        // Get the ID sent by the request 
        $id = $request->get('id');

        try {
            // Only SUPER_ADMIN can delete forums
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given forum
                $em = $this->getDoctrine()->getEntityManager();
                $certificate = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->find($id);

                $em->remove($certificate);
                $em->flush();

                // Send a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            //$response['cause'] = 'Kunne ikke slette forumet.';
            $response['cause'] = $e->getMessage(); // if you want to see the exception message. 

            return new JsonResponse($response);
        }

        // Send a respons to ajax 
        return new JsonResponse($response);
    }

    public function requestAction(Request $request)
    {

        // Get the ID sent by the request 
        $id = $request->get('id');

        try {
            // Only SUPER_ADMIN can delete forums
            if ($this->get('security.context')->isGranted('ROLE_USER')) {

                // This deletes the given forum
                $em = $this->getDoctrine()->getEntityManager();

                // A new certificate entity 
                $certificate = new CertificateRequest();

                // Find the user that sent the request 
                $user = $this->get('security.context')->getToken()->getUser();

                // Add the user to the certificate
                $certificate->setUser($user);

                // Store it in the database 
                $em->persist($certificate);
                $em->flush();

                // Send a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke lage en ny forespørsel.';
            //$response['cause'] = $e->getMessage(); // if you want to see the exception message. 

            return new JsonResponse($response);
        }

        // Send a respons to ajax 
        return new JsonResponse($response);
    }
}
