<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Subscriber;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;

class ReceiptController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets all Receipts
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Receipt",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Receipt')->findAll();
    }

    /**
     * Gets all Receipts from logged in user
     *
     * @return mixed
     *
     * @Get("/myreceipts")
     *
     * @ApiDoc(
     *     section="Receipt",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function myReceiptsAction()
    {
        $user = $this->getUser();
        return $user->getReceipts();
    }
}
