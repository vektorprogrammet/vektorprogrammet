<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\Department;
use AppBundle\Entity\Subscriber;
use AppBundle\Entity\SupportTicket;
use AppBundle\Event\ApplicationCreatedEvent;
use AppBundle\Event\SupportTicketCreatedEvent;
use AppBundle\Form\Type\ApplicationExistingUserType;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Form\Type\SupportTicketType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdmissionController extends Controller
{
    public function showAction(Request $request)
    {
        $admissionManager = $this->get('app.application_admission');
        $department = $admissionManager->getDepartment($request);

        $em = $this->getDoctrine()->getManager();
        $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($department);

        $teams = $em->getRepository('AppBundle:Team')->findByOpenApplicationAndDepartment($department);

        $application = new Application();

        $form = $this->createForm(ApplicationType::class, $application, array(
            'validation_groups' => array('admission'),
            'departmentId' => $department->getId(),
        ));

        // Find the relevant newsletter, based on what department the user is applying for
        $newsletter = $em->getRepository('AppBundle:Newsletter')->findCheckedByDepartment($department);

        // If the department has no active newsletter, the checkbox is removed.
        if ($newsletter === null) {
            $form->remove('wantNewsletter');
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $admissionManager->setCorrectUser($application);

            if ($application->getUser()->hasBeenAssistant()) {
                return $this->redirectToRoute('admission_existing_user');
            }

            // If the checkbox is checked, we want to subscribe to a newsletter
            if ($form['wantNewsletter']->getData() && $newsletter !== null) {
                $subscriber = new Subscriber();
                $subscriber->setName($application->getUser()->getFullName());
                $subscriber->setEmail($application->getUser()->getEmail());

                // Check if the user is already subscribed
                $alreadySubscribed = count($this->getDoctrine()->getRepository('AppBundle:Subscriber')->
                    findByEmailAndNewsletter($subscriber->getEmail(), $newsletter)) > 0;

                if (!$alreadySubscribed) {
                    $subscriber->setNewsletter($newsletter);
                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($subscriber);
                    $manager->flush();
                }
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
            'teams' => $teams,
            'form' => $form->createView(),
        ));
    }

    public function contactAction(Request $request, Department $department)
    {
        $supportTicket = new SupportTicket();
        $supportTicket->setDepartment($department);
        $contactForm = $this->createForm(new SupportTicketType(), $supportTicket, array(
            'action' => $this->generateUrl('admission_contact', array(
                'id' => $department->getId(),
            )),
        ));

        $contactForm->handleRequest($request);

        if ($contactForm->isValid()) {
            $this->get('event_dispatcher')->dispatch(SupportTicketCreatedEvent::NAME, new SupportTicketCreatedEvent($supportTicket));

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
