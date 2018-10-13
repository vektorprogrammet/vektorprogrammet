<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Signature;
use AppBundle\Form\Type\CreateSignatureType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CertificateController extends Controller
{
    /**
     * @Route(
     *     "/kontrollpanel/attest/{id}",
     *     name="certificate_show",
     *     defaults={"id": null},
     *     methods={"GET", "POST"}
     * )
     *
     * @param Request $request
     * @param Semester|null $semester
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Semester $semester = null)
    {
        if ($semester === null) {
            $semester = $this->getUser()->getDepartment()->getCurrentOrLatestSemester();
        }
        $department = $semester->getDepartment();
        $em = $this->getDoctrine()->getManager();

        $assistants = $em->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($department, $semester);

        $signature = $this->getDoctrine()->getRepository('AppBundle:Signature')->findByUser($this->getUser());
        $oldPath = '';
        if ($signature === null) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(new CreateSignatureType(), $signature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

            if ($isImageUpload) {
                $signaturePath = $this->get('app.file_uploader')->uploadSignature($request);
                $this->get('app.file_uploader')->deleteSignature($oldPath);

                $signature->setSignaturePath($signaturePath);
            } else {
                $signature->setSignaturePath($oldPath);
            }

            $signature->setUser($this->getUser());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($signature);
            $manager->flush();

            $this->addFlash('success', 'Signaturen ble lagret');
            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the the certificate requests
        $certificateRequests = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->findAll();

        return $this->render('certificate/index.html.twig', array(
            'certificateRequests' => $certificateRequests,
            'form' => $form->createView(),
            'signature' => $signature,
            'assistants' => $assistants,
            'currentSemester' => $semester,
        ));
    }
}
