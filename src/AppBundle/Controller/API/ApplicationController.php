<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Application;
use AppBundle\Entity\FieldOfStudy;
use AppBundle\Entity\User;
use AppBundle\Form\Type\ApplicationType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

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
    public function postAction(Request $request)
    {
        $user = new User();
        $application = new Application();

        $user->setFirstName($request->request->get('application[user][firstName]'));
        $user->setLastName($request->request->get('application[user][lastName]'));
        $user->setEmail($request->request->get('application[user][email]'));
        $user->setPhone($request->request->get('application[user][phone]'));
        $user->setFieldOfStudy($request->request->get('application[user][fieldOfStudy]'));

        //Dummy data
        $user->setGender(1);
        $application->setYearOfStudy(1);
        //

        $application->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($application);
        $em->flush();

        return $application;
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
