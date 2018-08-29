<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Role\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\CertificateRequest;
use AppBundle\Entity\Department;

class CertificateController extends Controller
{
    public function showAction()
    {
        // Finds all the the certificate requests
        $certificateRequests = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->findAll();
        $department = $this->getUser()->getDepartment();
        $currentSemester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department);

        return $this->render('certificate/index.html.twig', array(
            'certificateRequests' => $certificateRequests,
            'currentSemester'     => $currentSemester,
        ));
    }

    public function deleteAction(Request $request)
    {

        // Get the ID sent by the request
        $id = $request->get('id');

        // This deletes the given certificate
        $em = $this->getDoctrine()->getManager();
        $certificate = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->find($id);

        $em->remove($certificate);
        $em->flush();

        // Send a response back to AJAX
        $response['success'] = true;

        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function downloadAction(Semester $semester)
    {
        $em = $this->getDoctrine()->getManager();

        // Finds the department for the current logged in user
        $department = $this->getUser()->getDepartment();

        // Finds all the assistants of the associated semester and department (semester can be NULL)
        $assistants = $em->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($department, $semester);

        return $this->render('certificate/certificate_download.html.twig', array(
            'assistants'      => $assistants,
            'currentSemester' => $semester,
        ));
    }

    public function requestAction()
    {
        $em = $this->getDoctrine()->getManager();

        // A new certificate entity
        $certificate = new CertificateRequest();

        // Find the user that sent the request
        $user = $this->getUser();

        // Add the user to the certificate
        $certificate->setUser($user);

        // Store it in the database
        $em->persist($certificate);
        $em->flush();

        // Send a response back to AJAX
        $response['success'] = true;

        // Send a respons to ajax
        return new JsonResponse($response);
    }
}
