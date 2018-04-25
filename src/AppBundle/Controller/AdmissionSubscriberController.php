<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AdmissionSubscriber;
use AppBundle\Entity\Department;
use AppBundle\Form\Type\AdmissionSubscriberType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdmissionSubscriberController extends Controller
{
    /**
     * @Route("/interesseliste/{shortName}", name="interest_list", requirements={"shortName"="\w+"})
     * @Route("/interesseliste/{id}", name="interest_list_by_id", requirements={"id"="\d+"})
     *
     * @param Request $request
     * @param Department $department
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subscribePageAction(Request $request, Department $department)
    {
        $subscriber = new AdmissionSubscriber();
        $subscriber->setDepartment($department);

        $form = $this->createForm(AdmissionSubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (!$em->getRepository('AppBundle:AdmissionSubscriber')->findByEmailAndDepartment($subscriber->getEmail(), $department)) {
                $em->persist($subscriber);
                $em->flush();
            }

            $this->addFlash('success', $subscriber->getEmail().' har blitt meldt på interesselisten. Du vil få en e-post når opptaket starter');

            return $this->redirectToRoute('interest_list', ['shortName' => $department->getShortName()]);
        }

        return $this->render('admission_subscriber/subscribe_page.html.twig', [
            'department' => $department,
            'form' => $form->createView()
        ]);
    }

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
