<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Department;
use AppBundle\Entity\SupportTicket;
use AppBundle\Event\SupportTicketCreatedEvent;
use AppBundle\Form\Type\SupportTicketType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller
{
    /**
     * @Route("/kontakt", name="contact")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $_SERVER['http_client_ip'] = '82.102.27.50';

        return $this->render('contact/index.html.twig');
    }

    /**
     * @Route("/kontakt/melding/{id}", requirements={"id" = "\d+"}, name="contact_department")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Department $department
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contactAction(Request $request, Department $department)
    {
        $supportTicket = new SupportTicket();
        $form = $this->createForm(new SupportTicketType(), $supportTicket);
        $form->remove('captcha');

        $form->handleRequest($request);
        if ($form->isValid()) {
            $supportTicket->setDepartment($department);
            $this->get('event_dispatcher')
                ->dispatch(SupportTicketCreatedEvent::NAME, new SupportTicketCreatedEvent($supportTicket));

            $this->addFlash('success', 'Meldingen ble sendt!');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact_form.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
        ));
    }
}
