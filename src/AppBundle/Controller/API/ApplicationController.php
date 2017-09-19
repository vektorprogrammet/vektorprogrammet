<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ApplicationController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Application
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Application",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Application ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Application",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Application')->find($id);
    }

    /**
     * Gets all Applications
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Application",
     *     output="AppBundle\Entity\Application",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Application')->findAll();
    }

    /**
     * Creates a new Application
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Application",
     *     input="AppBundle\Form\Type\ApplicationType",
     *     statusCodes={
     *          201 = "Returned when successful",
     *          400 = "Bad request",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function postAction()
    {
    }

    /**
     * Updates an Application
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Application",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Application ID"
     *      }
     *     },
     *     input="AppBundle\Entity\Application",
     *     statusCodes={
     *          201 = "Returned when successful",
     *          400 = "Bad request",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function putAction(int $id)
    {
    }

    /**
     * Deletes an Application
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Application",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Application ID"
     *      }
     *     },
     *     statusCodes={
     *          204 = "Returned when successful",
     *          400 = "Bad request",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function deleteAction(int $id)
    {
    }
}
