<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignatureController extends BaseController
{
    public function showSignatureImageAction($imageName)
    {
        $user = $this->getUser();

        $signature = $this->getDoctrine()->getRepository('AppBundle:Signature')->findByUser($user);
        if ($signature === null) {
            throw new NotFoundHttpException('Signature not found');
        }

        $signatureImagePath = $signature->getSignaturePath();
        $signatureFileName = substr($signatureImagePath, strrpos($signatureImagePath, '/') + 1);
        if ($imageName !== $signatureFileName) {
            // Users can only view their own signatures
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse($this->container->getParameter('signature_images').'/'.$signatureFileName);
    }
}
