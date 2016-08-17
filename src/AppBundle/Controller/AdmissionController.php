<?php

namespace AppBundle\Controller;

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
        $departmentId = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $department = $em->getRepository('AppBundle:Department')->find($departmentId);

        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

        if ($semester !== null) {
            $application = new Application();

            $authenticated = false;

            // The Captcha should not appear if a user is authenticated, as said in the requirements
            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $authenticated = true;
            }

            $form = $this->createForm(new ApplicationType($department, $authenticated), $application, array(
                'validation_groups' => array('admission'),
            ));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $user = $this->getUser();

                if (is_null($user)) {
                    //Check if email belongs to an existing account and use that account
                    $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $application->getUser()->getEmail()));
                }
                if ($user !== null) {
                    // User is logged in, or a user is registered with the email
                    $userAssistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findBy(array('user' => $user));
                    if (empty($userAssistantHistory)) {
                        $application->setUser($user);
                    } else {
                        //If applicant has a user and has been an assistant before
                        return $this->redirect($this->generateUrl('admission_existing_user'));
                    }
                }

                $application->setSemester($semester);
                $em->persist($application);
                $em->flush();

                // Send a confirmation email with a copy of the application
                $emailMessage = \Swift_Message::newInstance()
                    ->setSubject('Søknad - Vektorassistent')
                    ->setFrom('rekruttering@vektorprogrammet.no')
                    ->setTo($application->getUser()->getEmail())
                    ->setBody($this->renderView('admission/admission_email.html.twig', array('application' => $application)));
                $this->get('mailer')->send($emailMessage);

                $request->getSession()->getFlashBag()->add('admission-notice',
                    'Søknaden din er registrert. En kvittering har blitt sendt til '.
                    $application->getUser()->getEmail().'. Lykke til!');

                return $this->redirect($this->generateUrl('admission_show_specific_department', array(
                    'id' => $departmentId,
                )));
            }

            return $this->render('admission/index.html.twig', array(
                'department' => $department,
                'semester' => $semester,
                'form' => $form->createView(),
            ));
        } else {
            return $this->render('admission/index.html.twig', array(
                'department' => $department,
            ));
        }
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
        $fromEmail = $this->container->getParameter('no_reply_email_contact_form');

        $contactForm->handleRequest($request);

        if ($contactForm->isValid()) {
            //send mail to department
            $message = \Swift_Message::newInstance()
                ->setSubject('Nytt kontaktskjema')
                ->setFrom($fromEmail)
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
        if (!sizeof($user->getAssistantHistories())) {
            return $this->render(':error:no_assistanthistory.html.twig');
        }

        $em = $this->getDoctrine()->getManager();
        $department = $user->getFieldOfStudy()->getDepartment();
        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department, new \DateTime());

        if (is_null($semester)) {
            return $this->render(':error:no_active_admission.html.twig');
        }

        $applicationRepo = $em->getRepository('AppBundle:Application');

        $application = $applicationRepo->findOneBy(array('user' => $user, 'semester' => $semester));
        if ($application === null) {
            $application = new Application();
        }
        $lastInterview = $em->getRepository('AppBundle:Interview')->findLatestInterviewByUser($user);

        $form = $this->createForm(new ApplicationExistingUserType(), $application, array(
            'validation_groups' => array('admission_existing'),
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->setUser($user);
            $application->setSemester($semester);
            $application->setPreviousParticipation(true);
            $application->setInterview($lastInterview);

            $em->persist($application);
            $em->flush();

            // Send a confirmation email with a copy of the application
            $emailMessage = \Swift_Message::newInstance()
                ->setSubject('Søknad - Vektorassistent')
                ->setFrom('rekruttering@vektorprogrammet.no')
                ->setTo($application->getUser()->getEmail())
                ->setBody($this->renderView('admission/admission_existing_email.html.twig', array('application' => $application)));
            $this->get('mailer')->send($emailMessage);

            $request->getSession()->getFlashBag()->add('admission-notice',
                'Søknaden din er registrert. En kvittering har blitt sendt til '.
                $application->getUser()->getEmail().'. Lykke til!');
        }

        return $this->render(':admission:existingUser.html.twig', array(
            'form' => $form->createView(),
            'department' => $department,
            'semester' => $semester,
            'user' => $user,
        ));
    }
}
