<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SponsorController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Sponsor
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Sponsor",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Sponsor ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Sponsor",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Sponsor')->find($id);
    }

    /**
     * Gets all Sponsors
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Sponsor",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Sponsor')->findAll();
    }

    /**
     * Creates a new Sponsor
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Sponsor",
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
     * Updates a Sponsor
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Sponsor",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Sponsor ID"
     *      }
     *     },
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
     * Deletes a Sponsor
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Sponsor",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Sponsor ID"
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
