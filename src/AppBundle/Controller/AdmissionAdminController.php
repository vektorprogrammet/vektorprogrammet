<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Form\Type\ApplicationType;
use AppBundle\Form\Type\NewUserType;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

/**
 * AdmissionAdminController is the controller responsible for administrative admission actions,
 * such as showing and deleting applications.
 *
 * @package AppBundle\Controller
 */
class AdmissionAdminController extends Controller {

    // The name of the default role a new user is given.
    const NEW_USER_ROLE = 'User';

    /**
     * Shows the admission admin page. Shows only applications for the department of the logged in user.
     * This works as the restricted admission management method, only allowing users to manage applications within their department.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request) {
        return $this->renderApplicants($request);
    }

    /**
     * Shows the admission admin page with applications from the given department.
     * This is the method is only accessible by users with sufficient rights to manage all departments.
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function showApplicationsByDepartmentAction(Request $request, $id){
        return $this->renderApplicants($request, $id);
	}

    public function renderApplicants(Request $request, $departmentId=null){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        // Get query strings for filtering applications
        $status = $request->query->get('status', 'new');
        $semesterId = $request->query->get('semester', null);
        if($semesterId === null){
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($user->getFieldOfStudy()->getDepartment());
        }else{
            $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        }

        // Finds all the departments
        $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

        if($departmentId === null){

            // Finds the department for the current logged in user
            $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();

        }else{

            $department = $this->getDoctrine()->getRepository('AppBundle:Department')->find($departmentId);
        }

        // Finds the name of the chosen semester. If no semester chosen display 'Alle'
        $semesterName = is_null($semester) ? 'Alle':$semester->getName();

        // Find all the semesters associated with the department
        $semesters =  $this->getDoctrine()->getRepository('AppBundle:Semester')->findAllSemestersByDepartment($department);

        // Finds the applicants for the given department filtered by interview status and semester
        $repository = $this->getDoctrine()->getRepository('AppBundle:Application');
        $yourApplicants = array();
        $interviewDistribution = array();
        switch($status) {
            case 'assigned':
                $applicants = $repository->findAssignedApplicants($department,$semester);
                foreach($applicants as $applicant){
                    $fullName = $applicant->getInterview()->getInterviewer()->getFirstName().' '.$applicant->getInterview()->getInterviewer()->getLastName();
                    if(array_key_exists($fullName,$interviewDistribution)){
                        $interviewDistribution[$fullName]++;
                    }else{
                        $interviewDistribution[$fullName] = 1;
                    }
                    if($applicant->getInterview()->getInterviewer() == $user){
                        $yourApplicants[] = $applicant;
                    }
                }
                arsort($interviewDistribution);
                $template = 'assigned_applications_table.html.twig';
                break;
            case 'interviewed':
                $applicants = $repository->findInterviewedApplicants($department,$semester);
                $template = 'interviewed_applications_table.html.twig';
                break;
            default:
                $applicants = $repository->findNewApplicants($department,$semester);
                $template = 'new_applications_table.html.twig';
                $status = 'new';
        }

        return $this->render('admission_admin/' . $template, array(
            'status' => $status,
            'applicants' => $applicants,
            'yourApplicants' => $yourApplicants,
            'interviewDistribution' => $interviewDistribution,
            'departments' => $allDepartments,
            'semesters' => $semesters,
            'semesterName' => $semesterName,
            'numOfApplicants' => sizeof($applicants),
            'departmentName' => $department->getShortName(),
        ));
    }

    /**
     * Deletes the given application.
     * This method is intended to be called by an Ajax request.
     *
     * @param $id
     * @return JsonResponse
     */
	public function deleteApplicationByIdAction($id){
		try {
			if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {
			
				// This deletes the given user
				$em = $this->getDoctrine()->getEntityManager();
				// Find the application by ID
				$application = $this->getDoctrine()->getRepository('AppBundle:Application')->find($id);
				$em->remove($application);
				$em->flush();
				
				// AJAX response
				$response['success'] = true;
			}
            // This allows someone of a different role(lower) to delete applications/interviews if they belong to the same department.
			// This functionality is not in use, as only the highest admin should be able to delete applications/interviews.
			elseif ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')){
				
				$em = $this->getDoctrine()->getEntityManager();
				$application = $this->getDoctrine()->getRepository('AppBundle:Application')->find($id);
				// Get the department of the application
				$department = $application->getStatistic()->getFieldOfStudy()->getDepartment();
				
				// Is the admin from the same department as the application?
				if ($this->get('security.context')->getToken()->getUser()->getFieldOfStudy()->getDepartment() === $department){
					
					$em->remove($application);
					$em->flush();
					// Send a respons to AJAX
					$response['success'] = true;
				
				}
				else {
					// Send a respons to AJAX
					$response['success'] = false;
					$response['cause'] = 'Du kan ikke slette en applikasjon som ikke er fra din avdeling.';
				}
			}
			else {
				// Send a respons to AJAX
				$response['success'] = false;
				$response['cause'] = 'Ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a respons to AJAX
			return new JsonResponse([
                'success' => false,
                'code'    => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
		}
		
		// Response to ajax
		return new JsonResponse( $response );
	}

    /**
     * Deletes the applications submitted as a list of ids through a form POST request.
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDeleteApplicationAction(Request $request){
        try {
            // Get the ids from the form
            $applicationIds = $request->request->get('application')['id'];

            // Get the application objects
            $em = $this->getDoctrine()->getEntityManager();
            $applications = $em->getRepository('AppBundle:Application')->findById($applicationIds);

            if ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {
                // Delete the applications
                foreach($applications as $application) {
                    $em->remove($application);
                }
                $em->flush();

                // AJAX response
                $response['success'] = true;
            }
            // This allows someone of a different role(lower) to delete applications/interviews if they belong to the same department.
            // This functionality is not in use, as only the highest admin should be able to delete applications/interviews.
            elseif ($this->get('security.context')->isGranted('ROLE_HIGHEST_ADMIN')) {
                $response['success'] = true;

                // Delete the applications
                foreach($applications as $application) {
                    $department = $application->getStatistic()->getFieldOfStudy()->getDepartment();
                    if($this->getUser()->getFieldOfStudy()->getDepartment() === $department) {
                        $em->remove($application);
                    } else {
                        $response['success'] = false;
                        $response['cause'] = 'Ikke tilstrekkelige rettigheter til å slette en eller flere av søknadene (utenfor din avdeling).';
                    }
                }

                $em->flush();
            }
            else {
                // Send a respons to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        }
        catch (\Exception $e) {
            // Send a respons to AJAX
            return new JsonResponse([
                'success' => false,
                'code'    => $e->getCode(),
                'cause' => 'En exception oppstod. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message.
            ]);
        }

        // Response to ajax
        return new JsonResponse($response);
    }

    /**
     * Creates an unactivated user for the given application.
     * This method is intended to be called by an Ajax request.
     * TODO: FIll in description
     *
     * @param $id
     * @return JsonResponse
     */
    public function createUnactivatedUserAction($id){
        try{
            $em = $this->getDoctrine()->getManager();

            $application = $em->getRepository('AppBundle:Application')->findApplicantById($id);
            $role = $em->getRepository('AppBundle:Role')->findOneByName(AdmissionAdminController::NEW_USER_ROLE);

            // Create the hash
            $createNewUserCode = bin2hex(openssl_random_pseudo_bytes(16));
            $hashedNewUserCode = hash('sha512', $createNewUserCode, false);

            // Copy information from the given application to a new user
            $user = new User();
            $user->setLastName($application->getLastName());
            $user->setFirstName($application->getFirstName());
            $user->setGender($application->getStatistic()->getGender());
            $user->setPhone($application->getPhone());
            $user->setFieldOfStudy($application->getStatistic()->getFieldOfStudy());
            $user->setEmail($application->getEmail());

            // Create Username from email, and make sure it's unique
            $new_username = explode("@", $application->getEmail())[0];
            $user_rep = $em->getRepository('AppBundle:User');
            $violator = $user_rep->findOneBy(
                array('user_name' => $new_username)
            );
            $postfix = 0;
            while($violator){
                $postfix++;
                $violator = $user_rep->findOneBy(
                    array('user_name' => ($new_username . $postfix))
                );
            }
            if($postfix){
                $new_username = $new_username . $postfix;
            }


            $user->setUserName($new_username);
            $user->setPassword($new_username);

            $user->setIsActive('0');
            $user->setNewUserCode($hashedNewUserCode);

            // Give the new user the default role
            $user->addRole($role);

            // Update the application
            $application->setUserCreated(true);

            // Update application statistic
            $application->getStatistic()->setAccepted(true);

            // Persist
            $em->persist($application);
            $em->persist($user);
            $em->flush();

            //Sends a email with the url for resetting the password
            //echo('127.0.0.1:8000/opprettbruker/'.$createNewUserCode.'');

            $this->sendNewUserEmail($createNewUserCode, $user->getEmail());

            return new JsonResponse([
                'success' => true,
            ]);
        }catch(\Exception $e){
            // If it is a integrity violation constraint (i.e a user with the email already exists)
            if($e->getPrevious()){ //If the error occurred when sending email, $e->getPrevious() will be null
                if($e->getPrevious()->getCode() == 23000) {
                    $message = 'En bruker med denne E-posten eksisterer allerede.';
                }
            } else {
                $message = 'En feil oppstod. Kontakt IT ansvarlig.';
            }

            return new JsonResponse([
                'success' => false,
                'cause' => $message
            ]);
        }

    }

    /**
     * TODO: FIll in description
     *
     * @param $createNewUserCode
     * @param $email
     */
    public function sendNewUserEmail($createNewUserCode, $email){
        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Opprett bruker på vektorprogrammet.no')
            ->setFrom($this->container->getParameter('no_reply_email_user_creation'))
            ->setTo($email)
            ->setBody($this->renderView('new_user/create_new_user_email.txt.twig', array('newUserURL' => $this->generateURL('admissionadmin_create_new_user', array( 'id' => $createNewUserCode), true))));
        $this->get('mailer')->send($emailMessage);
    }

    /**
     * TODO: FIll in description
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createNewUserAction(Request $request, $id){

        try{
            $repositoryUser = $this->getDoctrine()->getRepository('AppBundle:User');
            $hashedNewUserCode = hash('sha512', $id, false);
            $user = $repositoryUser->findUserByNewUserCode($hashedNewUserCode);

            $form = $this->createForm(new NewUserType(), $user);

            $form->handleRequest($request);

            //Checks if the form is valid
            if ($form->isValid()) {
                //Deletes the newUserCode, so it can only be used one time.
                $user->setNewUserCode(null);

                $user->setIsActive("1");

                //Updates the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //renders the login page, with a feedback message so that the user knows that the new password was stored.
                $feedback = 'Logg inn med din nye bruker';
                return $this->render('Login/login.html.twig', array('message' => $feedback,'error' => null, 'last_username' => $user->getUsername()));

            }
            //Render reset_password twig with the form.
            return $this->render('new_user/create_new_user.html.twig', array('form' => $form->createView(), 'firstName' => $user->getFirstName(), 'lastName' => $user->getLastName()));

        }catch(\Exception $e){
            return $this->redirect('/');
        }
    }

    public function createApplicationAction(Request $request){
        $em = $this->getDoctrine()->getEntityManager();
        $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();
        $currentSemester = null;
        try{
            $currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemesterByDepartment($department->getId());
        }catch(NoResultException $e){
            return $this->redirect($this->generateUrl('semesteradmin_show'));
        }catch(NonUniqueResultException $e){
            return $this->redirect($this->generateUrl('semesteradmin_show'));
        }
        $time = $currentSemester->getAdmissionStartDate();
        $time->modify('+1 day');//Workaround to reuse ApplicationType

        $application = new Application();
        $form = $this->createForm(new ApplicationType($department->getId(), $time, true), $application);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->setSubstituteCreated(0);
            $application->setUserCreated(0);
            $application->getStatistic()->setAccepted(0);
            $em->persist($application);
            $em->flush();

            $request->getSession()->getFlashBag()->add('admission-notice', 'Søknaden er registrert.');

            return $this->redirect($this->generateUrl('register_applicant', array(
                'id' => $department->getId(),
            )));
        }
        return $this->render(':admission_admin:create_application.html.twig', array(
            'department' => $department,
            'form' => $form->createView(),
        ));
    }
}
