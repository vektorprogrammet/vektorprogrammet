<?php

namespace AppBundle\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class NewsletterController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Newsletter
     *
     * @param int $id
     * @return mixed
     *
     * @ApiDoc(
     *     section="Newsletter",
     *     requirements={
     *      {
     *          "name"="id",
     *          "dataType"="integer",
     *          "requirement"="\d+",
     *          "description"="Newsletter ID"
     *      }
     *     },
     *     output="AppBundle\Entity\Newsletter",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Newsletter')->find($id);
    }

    /**
     * Gets all Newsletters
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Newsletter",
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function cgetAction()
    {
        return $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findAll();
    }


    /**
     * Get Newsletter by Department Short name
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Newsletter",
     *     requirements={
     *      {
     *          "name"="shortName",
     *          "dataType"="string",
     *          "description"="Department short name"
     *      }
     *     },
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */

    public function getDepartmentAction(string $shortName)
    {
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->findDepartmentByShortName($shortName);
        return $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department)->getId();
    }

}
