<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Subscriber;
use AppBundle\Form\Type\SubscribeToNewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriberController extends Controller
{
    public function showSubscribePageAction(Request $request, Newsletter $newsletter)
    {
        $subscriber = new Subscriber();

        $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alreadySubscribed = count($this->getDoctrine()->getRepository('AppBundle:Subscriber')->
                findByEmailAndNewsletter($subscriber->getEmail(), $newsletter)) > 0;

            if (!$alreadySubscribed) {
                $subscriber->setNewsletter($newsletter);

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($subscriber);
                $manager->flush();
            }

            $this->addFlash('success', $subscriber->getEmail().' ble registrert');

            return $this->redirectToRoute('newsletter_show_subscribe', array('id' => $newsletter->getId()));
        }

        return $this->render('newsletter/subscribe.html.twig', array(
            'newsletter' => $newsletter,
            'form' => $form->createView(),
        ));
    }

    public function showSubscribeAction(Department $department)
    {
        $newsletter = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);

        return $this->render('', array(
            'newsletters' => $newsletter,
        ));
    }

    public function showSubscribersAction(Newsletter $newsletter)
    {
        return $this->render('newsletter/show_subscribers.html.twig', array(
            'newsletter' => $newsletter,
        ));
    }

    public function adminUnsubscribeAction(Subscriber $subscriber)
    {
        $newsletterId = $subscriber->getNewsletter()->getId();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($subscriber);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_subscribers', array('id' => $newsletterId));
    }

    public function subscribeFormAction(Request $request, Department $department)
    {
        $em = $this->getDoctrine()->getManager();
        $newsletter = $em->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);

        $subscriber = new Subscriber($newsletter);

        if ($newsletter !== null) {
            $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber, array(
                'action' => $this->generateUrl('newsletter_subscribe', array(
                    'id' => $newsletter->getId(),
                )),
            ));
        } else {
            $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $alreadySubscribed = count($em->getRepository('AppBundle:Subscriber')->findByEmailAndNewsletter($subscriber->getEmail(), $newsletter)) > 0;
            if (!$alreadySubscribed) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($subscriber);
                $em->flush();
            }

            $this->addFlash('admission-notice', 'Takk for at du meldte deg på '.$newsletter->getName().'! '.$subscriber->getEmail().' ble registrert');

            return $this->redirectToRoute('admission_show_by_city_case_insensitive', array('city' => $department));
        }

        return $this->render('newsletter/subscribe_form.html.twig', array(
            'newsletter' => $newsletter,
            'form' => $form->createView(),
        ));
    }

    public function unsubscribeNewsletterAction($unsubscribeCode)
    {
        $subscriber = $this->getDoctrine()->getRepository('AppBundle:Subscriber')->findOneBy(array(
            'unsubscribeCode' => $unsubscribeCode,
        ));
        if ($subscriber === null) {
            throw new NotFoundHttpException();
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($subscriber);
        $manager->flush();

        return $this->redirectToRoute('unsubscribe_newsletter_confirmation', array(
            'email' => $subscriber->getEmail(),
            'newsletter' => $subscriber->getNewsletter()->getId(),
        ));
    }

    public function unsubscribeNewsletterConfirmationAction(Request $request)
    {
        $email = $request->query->get('email');
        $newsletterId = $request->query->get('newsletter');
        if ($email === null || $newsletterId === null) {
            throw new NotFoundHttpException();
        }
        $newsletter = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->find($newsletterId);
        if ($newsletter === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('newsletter/unsubscribe.html.twig', array(
            'email' => $email,
            'newsletter' => $newsletter,
        ));
    }
}
