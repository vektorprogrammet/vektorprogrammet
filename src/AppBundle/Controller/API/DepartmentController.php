<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DepartmentController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Department
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Department ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);
    }

    /**
     * Gets all Departments
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Department",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();
    }
}
