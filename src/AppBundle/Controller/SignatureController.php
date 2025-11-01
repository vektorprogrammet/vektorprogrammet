<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Signature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SignatureController extends BaseController
{
    private $entityManager;
    private $parameterBag;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $parameterBag
    ) {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }
    public function showSignatureImageAction($imageName)
    {
        $user = $this->getUser();

        $signature = $this->entityManager->getRepository(Signature::class)->findByUser($user);
        if ($signature === null) {
            throw new NotFoundHttpException('Signature not found');
        }

        $signatureImagePath = $signature->getSignaturePath();
        $signatureFileName = substr($signatureImagePath, strrpos($signatureImagePath, '/') + 1);
        if ($imageName !== $signatureFileName) {
            // Users can only view their own signatures
            throw new AccessDeniedException();
        }

        return new BinaryFileResponse($this->parameterBag->get('signature_images').'/'.$signatureFileName);
    }
}
