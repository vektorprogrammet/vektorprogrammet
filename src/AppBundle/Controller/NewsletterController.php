<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Letter;
use AppBundle\Entity\Newsletter;
use AppBundle\Form\Type\CreateLetterType;
use AppBundle\Form\Type\CreateNewsletterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsletterController extends Controller
{
    public function createAction(Request $request, Newsletter $newsletter = null)
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();

        if ($newsletter === null) {
            $newsletter = new Newsletter();
            $newsletter->setDepartment($department);
        }

        $form = $this->createForm(new CreateNewsletterType(), $newsletter);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();

            return $this->redirectToRoute('newsletter_show_all');
        }

        return $this->render('newsletter/create_newsletter.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAllNewslettersAction()
    {
        $department = $this->getUser()->getFieldOfStudy()->getDepartment();
        $newsletters = $this->getDoctrine()->getRepository('AppBundle:Newsletter')->findBy(array(
            'department' => $department,
        ));

        return $this->render('newsletter/show_all_newsletters.html.twig', array(
            'newsletters' => $newsletters,
            'department' => $department,
        ));
    }

    public function deleteAction(Newsletter $newsletter)
    {
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($newsletter);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_all');
    }

    public function showOnAdmissionPageAction(Newsletter $newsletter)
    {
        $isVisible = $newsletter->isShowOnAdmissionPage();
        $newsletter->setShowOnAdmissionPage(!$isVisible);
        $manager = $this->getDoctrine()->getManager();

        if ($isVisible === false) {
            $currentVisible = $manager->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($newsletter->getDepartment());
            if ($currentVisible !== null) {
                $currentVisible->setShowOnAdmissionPage(false);
                $manager->persist($currentVisible);
            }
        }

        $manager->persist($newsletter);
        $manager->flush();

        return $this->redirectToRoute('newsletter_show_all');
    }

    public function createLetterAction(Newsletter $newsletter, Request $request)
    {
        $letter = new Letter($newsletter);
        $form = $this->createForm(new CreateLetterType(), $letter);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('preview')->isClicked()) {
                return $this->render('newsletter/mail_template.html.twig', array(
                    'name' => '"NAVN NAVNESEN"',
                    'department' => $newsletter->getDepartment()->getShortName(),
                    'content' => $letter->getContent(),
                    'unsubscribeCode' => '1234',
                ));
            }
            $excludeApplicants = $form['excludeApplicants']->getData();

            $this->get('app.newsletter.manager')->send($letter, $excludeApplicants);

            return $this->render('newsletter/letter_sent_message.html.twig', array(
                'letter' => $letter,
            ));
        }

        return $this->render('newsletter/create_letter.html.twig', array('form' => $form->createView()));
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
}
