<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Signature;
use AppBundle\Form\Type\CreateSignatureType;
use AppBundle\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CertificateController extends BaseController
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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $department = $this->getDepartmentOrThrow404();
        $semester = $this->getSemesterOrThrow404();
        $em = $this->getDoctrine()->getManager();

        $assistants = $em->getRepository('AppBundle:AssistantHistory')->findByDepartmentAndSemester($department, $semester);

        $signature = $this->getDoctrine()->getRepository('AppBundle:Signature')->findByUser($this->getUser());
        $oldPath = '';
        if ($signature === null) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(CreateSignatureType::class, $signature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

            if ($isImageUpload) {
                $signaturePath = $this->get(FileUploader::class)->uploadSignature($request);
                $this->get(FileUploader::class)->deleteSignature($oldPath);

                $signature->setSignaturePath($signaturePath);
            } else {
                $signature->setSignaturePath($oldPath);
            }

            $signature->setUser($this->getUser());
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($signature);
            $manager->flush();

            $this->addFlash('success', 'Signatur og evt. kommentar ble lagret');
            return $this->redirect($request->headers->get('referer'));
        }

        // Finds all the the certificate requests
        $certificateRequests = $this->getDoctrine()->getRepository('AppBundle:CertificateRequest')->findAll();

        return $this->render('certificate/index.html.twig', array(
            'certificateRequests' => $certificateRequests,
            'form' => $form->createView(),
            'signature' => $signature,
            'assistants' => $assistants,
            'department' => $department,
            'currentSemester' => $semester,
        ));
    }
}
