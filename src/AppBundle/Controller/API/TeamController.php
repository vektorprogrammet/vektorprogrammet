<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class TeamController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Department
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Team",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Team ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Team",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Team')->find($id);
    }

    /**
     * Gets all Teams
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Team",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Team')->findAll();
    }
}
