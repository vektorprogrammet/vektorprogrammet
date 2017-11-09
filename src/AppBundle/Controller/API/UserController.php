<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual User
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="User",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="User ID"
     *      }
     *     },
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
    }

    /**
     * Gets all Users
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="User",
     *     output="AppBundle\Entity\User",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
    }
}