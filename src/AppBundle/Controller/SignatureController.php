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
        if ($signature === null){
            $signature = new Signature();
        }

        $form = $this->createForm(new CreateSignatureType(), $signature);

        //First move the signature image file to its folder
        $targetFolder = $this->container->getParameter('signature_images').'/';
        //Create a FileUploader with target folder and allowed file types as parameters
        $uploader = new FileUploader($targetFolder, ['image/gif', 'image/jpeg', 'image/png']);
        //Move the file to target folder
        $result = $uploader->upload($request);
        //return new Response(var_dump($result));
        //Get the path of the image file as now on the server:  todo: now assumes only one image is contained in the request, as it should be.
        dump([array_keys($result)]);
        $path = $result[array_keys($result)[0]]; //todo: duplicated code this line and those above it. see editProfilePhotoAction in ProfileController
        $signature->setSignaturePath($path);


        return $this->render('certificate/signature_picture_upload.html.twig', array(
            'form' => $form->createView(),
            'signature' => $signature
        ));
    }
}
