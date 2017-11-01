<?php

namespace AppBundle\Controller\API;

use AppBundle\Entity\Subscriber;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

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
     * Get Newsletter by Department Id
     *
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
     *     statusCodes={
     *          200 = "Returned when successful",
     *          404 = "Return when not found",
     *          500 = "Internal server error"
     *     }
     * )
     */

    public function getDepartmentAction(int $id)
    {
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($id);
        return $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);
    }

    /**
     * Creates a new Subscriber to Newsletter
     *
     * @return mixed
     *
     * @ApiDoc(
     *     section="Subscriber",
     *
     *     statusCodes={
     *          201 = "Returned when successful",
     *          400 = "Bad request",
     *          500 = "Internal server error"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $subscriber = new Subscriber();
        $newsletterId = $request->request->get('newsletterId');
        $newsletter = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->find($newsletterId);

        $subscriber->setName($request->request->get('name'));
        $subscriber->setEmail($request->request->get('email'));

        $alreadySubscribed = count($this->getDoctrine()->getRepository('AppBundle:Subscriber')->
            findByEmailAndNewsletter($subscriber->getEmail(), $newsletter)) > 0;

        if (!$alreadySubscribed) {
            $subscriber->setNewsletter($newsletter);

            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();
        }

        return $subscriber;
    }

}
