<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionSubscriber;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdmissionSubscriberController extends Controller
{
    /**
     * @Route("/opptak/notification", name="admission_subscribe")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subscribeAction(Request $request)
    {
        $email = $request->request->get('email');
        $departmentId = $request->request->get('department');
        if (!$email || !$departmentId) {
            return new JsonResponse("Email or department missing", 400);
        }
        $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);
        if (!$department) {
            return new JsonResponse("Invalid department", 400);
        }

        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);
        $subscriber->setEmail($email);

        $errors = $this->get('validator')->validate($subscriber);
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, 400);
        }

        $em = $this->getDoctrine()->getManager();
        if (!$em->getRepository('AppBundle:AdmissionSubscriber')->findByEmailAndDepartment($email, $department)) {
            $em->persist($subscriber);
            $em->flush();
        }

        return new JsonResponse(null, 201);
    }

    /**
     * @Route("/opptak/notification/unsubscribe/{code}", name="admission_unsibscribe")
     * @param string $code
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unsubscribeAction($code)
    {
        $subscriber = $this->getDoctrine()->getRepository('AppBundle:AdmissionSubscriber')->findByUnsubscribeCode($code);
        $this->addFlash('title', 'Opptaksvarsel - Avmelding');
        if (!$subscriber) {
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak");
        } else {
            $email = $subscriber->getEmail();
            $this->addFlash('message', "Du vil ikke lengre motta varsler om opptak på $email");
            $em = $this->getDoctrine()->getManager();
            $em->remove($subscriber);
            $em->flush();
        }

        return $this->redirectToRoute('confirmation');
    }
}
