<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class FrontEndController extends BaseController
{
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    public function indexAction()
    {
        $indexFile = $this->kernel->getRootDir() . '/../client/build/index.html';
        if (!file_exists($indexFile)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($indexFile);
    }
}
