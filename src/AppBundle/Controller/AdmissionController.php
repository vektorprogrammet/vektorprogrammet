<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ApplicationInfo;
use AppBundle\Entity\User;
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
		
		$semesters = $em->getRepository('AppBundle:Semester')
							->findAllSemestersByDepartment($departmentId);

		$fieldOfStudyList = $em->getRepository('AppBundle:FieldOfStudy')
			->findByDepartment(array('department' => $departmentId), array('short_name' => 'ASC'));

		$today = new DateTime("now");
		
		$currentSemester = null;

		foreach ($semesters as $semester) {
		
			$semesterStartDate = $semester->getAdmissionStartDate();
			$semesterEndDate = $semester->getAdmissionEndDate();
			
			if ($semesterStartDate < $today && $today < $semesterEndDate) {
				$currentSemester = $semester;
				break;
			}
			
		}
		
		if ( !empty($currentSemester) ) {
			
			$applicationInfo = new ApplicationInfo();
			$user = new User();
			$applicationInfo->setUser($user);

			dump($applicationInfo);

			$authenticated = false;
			//TODO:Add captcha to new form
			// The Captcha should not appear if a user is authenticated, as said in the requirements 
			if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
				$authenticated = true;
			}
			
			return $this->render('admission/index.html.twig', array(
				'department' => $department,
				'semester' => $currentSemester,
				'fieldOfStudyList' => $fieldOfStudyList,
			));
		}
		else {
			return $this->render('admission/index.html.twig', array(
				'department' => $department,
			));
		}
		
    }

    /**
     * Handle new application form post request
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function newApplicantAction(Request $request){
        $application = array(
            'firstName' => $request->get('firstName'),
            'lastName' => $request->get('lastName'),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'gender' => $request->get('gender'),
            'fieldOfStudy' => $request->get('fieldOfStudy'),
            'yearOfStudy' => $request->get('yearOfStudy'),
            'departmentId' => $request->get('department'),
        );


        if($this->applicationIsValid($application)){
            $em = $this->getDoctrine()->getManager();

            $userByEmail = null;

            try{
                $userByEmail = $em->getRepository('AppBundle:User')->findUserByEmail($application['email']);
            }catch(NoResultException $e){}

            $applicant = null;
            if($userByEmail !== null){
                $applicant = $userByEmail;
            }else{
                $fieldOfStudy = $em->getRepository('AppBundle:FieldOfStudy')->find($application['fieldOfStudy']);

                $applicant = new User();
                $applicant->setFirstName($application['firstName']);
                $applicant->setLastName($application['lastName']);
                $applicant->setEmail($application['email']);
                $applicant->setPhone($application['phone']);
                $applicant->setGender($application['gender']);
                $applicant->setFieldOfStudy($fieldOfStudy);
                $applicant->setUserName($application['email']);
                $em->persist($applicant);
            }

            $semester = $em->getRepository('AppBundle:Semester')->findSemesterWithActiveAdmissionByDepartment($application['departmentId']);

            $applicationInfo = new ApplicationInfo();
            $applicationInfo->setUser($applicant);
            $applicationInfo->setYearOfStudy($application['yearOfStudy']);
            $applicationInfo->setSemester($semester);

            $em->persist($applicationInfo);
            $em->flush();

            $request->getSession()->getFlashBag()->add('admission-notice', 'Søknaden din er registrert. Lykke til!');
        }else{
            $request->getSession()->getFlashBag()->add('error-notice', 'Det her skjedd en feil. Vennligst prøv igjen.');
        }

        return $this->redirect($this->generateUrl('admission_show_specific_department', array(
            'id' => $application['departmentId'],
        )));

    }

    private function applicationIsValid($application){
        if(strlen($application['firstName']) < 2 || strlen($application['lastName']) < 2 || strlen($application['phone']) < 8 || strlen($application['email']) < 3)return false;
        if( !($application['gender'] === '0' || $application['gender'] === '1') )return false;
        if(!is_numeric($application['fieldOfStudy']) || !is_numeric($application['yearOfStudy']))return false;
        return true;
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
