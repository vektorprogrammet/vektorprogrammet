<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Signature;
use AppBundle\FileSystem\FileUploader;
use AppBundle\Form\Type\CreateSignatureType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SignatureController extends Controller
{
    public function showAction(Request $request)
    {

        $signature = $this->getDoctrine()->getRepository('AppBundle:Signature')->findByUser($this->getUser());
        $oldPath = "";
        if ($signature === null) {
            $signature = new Signature();
        } else {
            $oldPath = $signature->getSignaturePath();
        }

        $form = $this->createForm(new CreateSignatureType(), $signature);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()){
        $isImageUpload = $request->files->get('create_signature')['signature_path'] !== null;

        if ($isImageUpload) {
            //First move the signature image file to its folder
            $targetFolder = $this->container->getParameter('signature_images') . '/';
            //Create a FileUploader with target folder and allowed file types as parameters
            $uploader = new FileUploader($targetFolder, ['image/gif', 'image/jpeg', 'image/png']);
            //Move the file to target folder
    
            $result = $uploader->upload($request);
            $path = $result[array_keys($result)[0]]; //todo: duplicated code this line and those above it. see editProfilePhotoAction in ProfileController
            $signature->setSignaturePath($path);
        } else {
            $signature->setSignaturePath($oldPath);
        }

        $signature->setUser($this->getUser());
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($signature);
        $manager->flush();
    }

        return $this->render('certificate/signature_picture_upload.html.twig', array(
            'form' => $form->createView(),
            'signature' => $signature
        ));
    }
}
