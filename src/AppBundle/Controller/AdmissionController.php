<?php

namespace AppBundle\Controller;

use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Form\Type\ApplicationExistingUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Entity\Application;
use AppBundle\Form\Type\ContactType;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Department;

class AdmissionController extends Controller
{
    public function showAction(Request $request)
    {
        $admissionManager = $this->get('app.application_admission');
        $department = $admissionManager->getDepartment($request);
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

        $application = new Application();

        $form = $this->createForm(ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $admissionManager->setCorrectUser($application, $user);

            if ($application->getUser()->hasBeenAssistant()) {
                return $this->redirectToRoute('admission_existing_user');
            }

            $application->setSemester($semester);
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            return $this->redirectToRoute('admission_show_specific_department', array('id' => $department->getId()));
        }

        return $this->render('admission/index.html.twig', array(
            'department' => $department,
            'semester' => $semester,
            'form' => $form->createView(),
        ));
    }

    public function contactAction(Request $request, Department $department)
    {
        $contact = new Contact();
        $contactForm = $this->createForm(new ContactType(), $contact, array(
            'action' => $this->generateUrl('admission_contact', array(
                'id' => $department->getId(),
            )),
        ));

        // Get the sender email from the parameter file
        $fromEmail = array($this->container->getParameter('no_reply_email_contact_form') => 'Kontaktskjema Vektorprogrammet');

        $contactForm->handleRequest($request);

        if ($contactForm->isValid()) {
            //send mail to department
            $message = \Swift_Message::newInstance()
                ->setSubject('Nytt kontaktskjema')
                ->setFrom($fromEmail)
                ->setReplyTo($contact->getEmail())
                ->setTo($department->getEmail())
                ->setBody($this->renderView('admission/contactEmail.txt.twig', array('contact' => $contact)));
            $this->get('mailer')->send($message);

            //send receipt mail back to sender
            $receipt = \Swift_Message::newInstance()
                ->setSubject('Kvittering for kontaktskjema')
                ->setFrom($fromEmail)
                ->setTo($contact->getEmail())
                ->setBody($this->renderView('admission/receiptEmail.txt.twig', array('contact' => $contact)));
            $this->get('mailer')->send($receipt);

            //popup text after completion
            $request->getSession()->getFlashBag()->add('contact-notice', 'Kontaktforespørsel sendt, takk for henvendelsen!');

            //redirect to avoid re-posting the form
            return $this->redirect($this->generateUrl('admission_show_specific_department', array(
                'id' => $department->getId(),
            )));
        }

        return $this->render('admission/contact.html.twig', array(
            'contactForm' => $contactForm->createView(),
            'department' => $department,
        ));
    }

    public function existingUserAdmissionAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $admissionManager = $this->get('app.application_admission');

        if ($res = $admissionManager->renderErrorPage($user)) {
            return $res;
        }

        $application = $admissionManager->createApplicationForExistingAssistant($user);

        $form = $this->createForm(new ApplicationExistingUserType(), $application, array(
            'validation_groups' => array('admission_existing'),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($application);
            $em->flush();

            $this->get('event_dispatcher')->dispatch(ApplicationCreatedEvent::NAME, new ApplicationCreatedEvent($application));

            return $this->redirectToRoute('admission_existing_user');
        }

        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($user->getDepartment());

        return $this->render(':admission:existingUser.html.twig', array(
            'form' => $form->createView(),
            'department' => $user->getDepartment(),
            'semester' => $semester,
            'user' => $user,
        ));
    }
}
