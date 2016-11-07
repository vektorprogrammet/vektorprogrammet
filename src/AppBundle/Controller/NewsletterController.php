<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\Letter;
use AppBundle\Entity\Newsletter;
use AppBundle\Entity\Subscriber;
use AppBundle\Form\Type\CreateLetterType;
use AppBundle\Form\Type\SubscribeToNewsletterType;
use AppBundle\Form\Type\CreateNewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterController extends Controller
{
    public function createAction(Request $request, Newsletter $newsletter = null)
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();

        if($newsletter == null){
            $newsletter = new Newsletter();
            $newsletter->setDepartment($department);
        }

        $form = $this->createForm(new CreateNewsletterType(), $newsletter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($newsletter);
            $manager->flush();

            return $this->redirectToRoute('newsletter_show_all');
        }

        return $this->render('newsletter/create_newsletter.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function showAllNewslettersAction()
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        $newsletters = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findBy(array(
            'department' => $department
        ));

        return $this->render('newsletter/show_all_newsletters.html.twig', array(
            'newsletters' => $newsletters,
            'department' => $department
        ));
    }

    public function showSubscribePageAction(Request $request, Newsletter $newsletter){
        $subscriber = new Subscriber();
        $unsubscribeCode = bin2hex(openssl_random_pseudo_bytes(12));

        $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $subscriber->setNewsletter($newsletter);
            $subscriber->setUnsubscribeCode($unsubscribeCode);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($subscriber);
            $manager->flush();

            $this->addFlash('success', $subscriber->getEmail() . ' ble registrert');

            return $this->redirectToRoute('newsletter_show_subscribe', array('id' => $newsletter->getId()));
        }

        return $this->render('newsletter/subscribe.html.twig', array(
            'newsletter' => $newsletter,
            'form' => $form->createView()
        ));
    }

    public function showSubscribeAction(Department $department)
    {
        $newsletter = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);
        dump($newsletter);

        return $this->render('', array(
            'newsletters' => $newsletter
        ));
    }

    public function showSubscribersAction(Newsletter $newsletter)
    {
        return $this->render('newsletter/show_subscribers.html.twig', array(
            'newsletter' => $newsletter
        ));
    }

    public function deleteAction(Newsletter $newsletter)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($newsletter);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_all');
    }

    public function adminUnsubscribeAction(Subscriber $subscriber)
    {
        $newsletterId = $subscriber->getNewsletter()->getId();

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($subscriber);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_subscribers', array('id' => $newsletterId));

    }

    public function showOnAdmissionPageAction(Newsletter $newsletter)
    {
        $isVisible = $newsletter->isShowOnAdmissionPage();
        $newsletter->setShowOnAdmissionPage(!$isVisible);
        $manager = $this->getDoctrine()->getManager();

        if ($isVisible === false){
            $currentVisible = $manager->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($newsletter->getDepartment());
            if ($currentVisible !== null)
            {
                $currentVisible->setShowOnAdmissionPage(false);
                $manager->persist($currentVisible);
            }
        }

        $manager->persist($newsletter);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_all');
    }

    public function subscribeFormAction(Request $request, Department $department)
    {
        $manager = $this->getDoctrine()->getManager();
        $newsletter = $manager->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);

        $subscriber = new Subscriber();

        $form = $this->createForm(new SubscribeToNewsletterType(), $subscriber);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $subscriber->setNewsletter($newsletter);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($subscriber);
            $manager->flush();

            $this->addFlash('success', $subscriber->getEmail() . ' ble registrert');

            return $this->redirectToRoute('admission_show_specific_department', array('id' => $department->getId()));
        }

        return $this->render('newsletter/subscribe_form.html.twig', array(
            'newsletter' => $newsletter,
            'form' => $form->createView()
        ));

    }
    public function createLetterAction(Newsletter $newsletter, Request $request)
    {
        $letter = new Letter();
        $letter->setNewsletter($newsletter);
        $form = $this->createForm(new CreateLetterType(), $letter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if (true === $form->get('preview')->isClicked()) {
                return $this->render(
                   'newsletter/mail_template.html.twig',
                   array(
                       'name' => '"NAVN NAVNESEN"',
                       'department' => $newsletter->getDepartment()->getShortName(),
                       'content' => $letter->getContent(),
                       'unsubscribeCode' => '1234'
                   )
               );
            }
            $letter->setTimestamp(new \DateTime());
            $letter->setRecipientCount(count($newsletter->getSubscribers()));
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($letter);
              $manager->flush();

            foreach ($newsletter->getSubscribers() as $subscriber){

                $message = \Swift_Message::newInstance()
                    ->setSubject($letter->getTitle())
                    ->setFrom(array(
                        $newsletter->getDepartment()->getEmail()=>'Vektorprogrammet'
                    ))
                    ->setTo($subscriber->getEmail())
                    ->setBody(
                        $this->renderView(

                            'newsletter/mail_template.html.twig',
                            array(
                                'name' => $subscriber->getName(),
                                'department' => $newsletter->getDepartment()->getShortName(),
                                'content' => $letter->getContent(),
                                'unsubscribeCode' =>$subscriber->getUnsubscribeCode()
                            )
                        ),
                        'text/html'
                    )
                    ->setContentType('text/html');

                $this->get('mailer')->send($message);
            }

            return $this->render('newsletter/letter_sent_message.html.twig', array(
                'letter' => $letter,
            ));
        }
        return $this->render('newsletter/create_letter.html.twig', array('form'=>$form->createView()));

    }
    public function showLettersAction(Newsletter $newsletter)
    {
        return $this->render('newsletter/show_letters.html.twig', array(
            'newsletter' => $newsletter,
        ));
    }
    public function showLetterContentAction(Letter $letter)
    {
        return $this->render('newsletter/show_letter_content.html.twig', array(
            'letter' => $letter,
        ));
    }
    public function unsubscribeNewsletterAction($unsubscribeCode)
    {
        $subscriber = $this->getDoctrine()->getRepository('AppBundle:Subscriber')->findOneBy(array(
            'unsubscribeCode' => $unsubscribeCode,
        ));
        if ($subscriber === null)
        {
            throw new NotFoundHttpException();
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($subscriber);
        $manager->flush();
        return $this->render('newsletter/unsubscribe.html.twig', array(
            'email' => $subscriber->getEmail(),
            'name' => $subscriber->getName(),
            'newsletter' => $subscriber->getNewsletter(),
        ));

    }



}
