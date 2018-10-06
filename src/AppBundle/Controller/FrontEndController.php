<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontEndController extends BaseController
{
    public function indexAction()
    {
        $indexFile = $this->get('kernel')->getRootDir() . '/../client/build/index.html';
        if (!file_exists($indexFile)) {
            throw new NotFoundHttpException();
        }

        return new BinaryFileResponse($indexFile);
    }
}
