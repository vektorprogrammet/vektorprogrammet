<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApplicationInfo;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Entity\Application;
use AppBundle\Entity\ApplicationStatistics;
use AppBundle\Form\Type\ContactType;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Department;

class AdmissionController extends Controller {

    public function showAction(Request $request) {


        $departmentId = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $department = $em->getRepository('AppBundle:Department')
            ->find($departmentId);

        $semester = null;
        try{
            $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($departmentId);
        }catch(NoResultException $e){}

        if ( $semester !== null ) {

            $application = new ApplicationInfo();

            $authenticated = false;

            // The Captcha should not appear if a user is authenticated, as said in the requirements
            if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $authenticated = true;
            }

            $form = $this->createForm(new ApplicationType($departmentId, $authenticated), $application);

            $form->handleRequest($request);

            if ($form->isValid()) {
                //Check if email belongs to an existing account and use that account
                try{
                    $oldUser = $em->getRepository('AppBundle:User')->findUserByEmail($application->getUser()->getEmail());
                }catch(NoResultException $e){$oldUser = null;}
                if($oldUser !== null){
                    if(!$oldUser->getIsActive()){
                        $application->setUser($oldUser);
                    }else{
                        $oldUserAssistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findBy(array('user'=>$oldUser));
                        if(empty($oldUserAssistantHistory)){
                            $application->setUser($oldUser);
                        }else{
                            //If applicant has a user and has been an assistant before
                            //TODO: Change this path to update the applicationInfo form
                            return $this->redirect($this->generateUrl('login_route'));
                        }
                    }
                }

                $application->setSemester($semester);
                $em->persist($application);
                $em->flush();

                $request->getSession()->getFlashBag()->add('admission-notice', 'Søknaden din er registrert. Lykke til!');

                return $this->redirect($this->generateUrl('admission_show_specific_department', array(
                    'id' => $departmentId,
                )));
            }

            return $this->render('admission/index.html.twig', array(
                'department' => $department,
                'semester' => $semester,
                'form' => $form->createView(),
            ));
        }
        else {
            return $this->render('admission/index.html.twig', array(
                'department' => $department,
            ));
        }

    }

    public function contactAction(Request $request, Department $department){

        $contact = new Contact();
        $contactForm = $this->createForm(new ContactType(), $contact, array (
            'action' => $this->generateUrl('admission_contact', array(
                'id' => $department->getId()
            ))
        ));

        // Get the sender email from the parameter file
        $fromEmail = $this->container->getParameter('no_reply_email_contact_form');

        $contactForm->handleRequest($request);

        if ($contactForm->isValid()){
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

}
