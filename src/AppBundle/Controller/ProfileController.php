<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\EditUserType;
use AppBundle\Form\Type\EditUserAdminType;
use AppBundle\Form\Type\EditUserPasswordType;
use AppBundle\FileSystem\FileUploader;
use Symfony\Component\HttpFoundation\Response;
use Ps\PdfBundle\Annotation\Pdf;
use Symfony\Component\HttpFoundation\JsonResponse;
use \DateTime;

class ProfileController extends Controller {

	public function deactivateUserAction(Request $request){
		
		// Get the ID sent by the request 
		$id = $request->get('id');
		
		try {
			// Only SUPER_ADMIN can activate users
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// Find the given user 
				$em = $this->getDoctrine()->getEntityManager();
				
				// Find the user with the given ID 
				$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
				
				// set user active level
				$user->setIsActive(0);
				
				$em->flush();
				
				// Send a response back to AJAX
				$response['success'] = true;
			}
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			$response['success'] = false;
			$response['cause'] = 'Kunne ikke endre rettighetene.';
			//$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
	}
	
	public function activateUserAction(Request $request){
		
		// Get the ID sent by the request 
		$id = $request->get('id');
		
		try {
			// Only SUPER_ADMIN can activate users
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// Find the given user 
				$em = $this->getDoctrine()->getEntityManager();
				
				// Find the user with the given ID 
				$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
				
				// set user active level
				$user->setIsActive(1);
				
				$em->flush();
				
				// Send a response back to AJAX
				$response['success'] = true;
			}
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			$response['success'] = false;
			$response['cause'] = 'Kunne ikke endre rettighetene.';
			//$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
	}
    
	
	public function promoteToTeamMemberAction(Request $request){
		
		// Get the ID sent by the request 
		$id = $request->get('id');
	
		try {
			// Only SUPER_ADMIN can edit roles 
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// Find the given user 
				$em = $this->getDoctrine()->getEntityManager();
				$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
				
				// Find all roles
				$roles =  $this->getDoctrine()->getRepository('AppBundle:Role')->findAll();
				
				// Remove all the roles from the user
				foreach($roles as $role){
					$user->removeRole($role);
					$role->removeUser($user);
				}
				
				// Find the admin role
				$adminRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole("ROLE_ADMIN");
				
				// We have to add both the role and the user to eachother due to many-to-many relations
				
				// Add the user to the adminrole 
				$adminRole->addUser($user);
				
				// Add the adminrole to the user 
				$user->addRole($adminRole);
				
				$em->flush();
				
				// Send a response back to AJAX
				$response['success'] = true;
			}
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			$response['success'] = false;
			//$response['cause'] = 'Kunne ikke endre rettighetene.';
			$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
	}
	
	public function promoteToAssistentAction(Request $request){

		// Get the ID sent by the request 
		$id = $request->get('id');
	
		try {
			// Only SUPER_ADMIN can edit roles 
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// Find the given user 
				$em = $this->getDoctrine()->getEntityManager();
				$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
				
				// Find all roles
				$roles =  $this->getDoctrine()->getRepository('AppBundle:Role')->findAll();
				
				// Remove all the roles from the user
				foreach($roles as $role){
					$user->removeRole($role);
					$role->removeUser($user);
				}
				
				// Find the assistent role
				$assistentRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole("ROLE_USER");
				
				// We have to add both the role and the user to eachother due to many-to-many relations
				
				// Add the user to the role
				$assistentRole->addUser($user);
				
				// Add the role to the user
				$user->addRole($assistentRole);
				
				
				$em->flush();
				
				// Send a response back to AJAX
				$response['success'] = true;
			}
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			$response['success'] = false;
			//$response['cause'] = 'Kunne ikke endre rettighetene.';
			$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
		
	}
	
	public function promoteToAdminAction(Request $request){

		// Get the ID sent by the request 
		$id = $request->get('id');
	
		try {
			// Only SUPER_ADMIN can edit roles 
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// Find the given user 
				$em = $this->getDoctrine()->getEntityManager();
				$user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
				
				// Find all roles
				$roles =  $this->getDoctrine()->getRepository('AppBundle:Role')->findAll();
				
				// Remove all the roles from the user
				foreach($roles as $role){
					$user->removeRole($role);
					$role->removeUser($user);
				}
				
				// Find the assistent role
				$assistentRole = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole("ROLE_SUPER_ADMIN");
				
				// We have to add both the role and the user to eachother due to many-to-many relations
				
				// Add the user to the role
				$assistentRole->addUser($user);
				
				// Add the role to the user
				$user->addRole($assistentRole);
				
				
				$em->flush();
				
				// Send a response back to AJAX
				$response['success'] = true;
			}
			else {
				// Send a response back to AJAX
				$response['success'] = false;
				$response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
			}
		}
		catch (\Exception $e) {
			// Send a response back to AJAX
			$response['success'] = false;
			//$response['cause'] = 'Kunne ikke endre rettighetene.';
			$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
		
	}
	
	/**
	 * @Pdf()
	 */
	public function downloadCertificateAction(Request $request){
		
		/*
		This method uses the bundle found here: https://github.com/psliwa/PdfBundle/blob/master/Controller/ExampleController.php
		
		It takes the twig file (download_certificate.pdf.twig) and converts it to a downloadable PDF. Follow the link to read more about configuration
		and how it works. 
		
		Here is an example controller: https://github.com/psliwa/PdfBundle/blob/master/Controller/ExampleController.php
		
		*/
		
		// Get the variable sent by the URL
		$id = $request->get('id');
		
		// Only ROLE_SUPER_ADMIN can create certificates 
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
			// Find the current time 
			$today = new DateTime('now');
			
			$em = $this->getDoctrine()->getEntityManager();
			
			// Find the user that is associated with the ID sent by the URL request 
			$user = $em->getRepository('AppBundle:User')->find($id);
			
			// Fetch the assistant history of the user
			$assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);
			
			// Find the work history of the user
			$workHistory =  $em->getRepository('AppBundle:WorkHistory')->findByUser($user);
			
			$facade = $this->get('ps_pdf.facade');
			$response = new Response();
			
			$this->render('profile/download_certificate.pdf.twig', array(
				'user' => $user,
				'today' => $today,
				'assistantHistory' => $assistantHistory,
				'workHistory' => $workHistory,
			), $response);
			
			$xml = $response->getContent();
			$content = $facade->render($xml);
			return new Response($content, 200, array('content-type' => 'application/pdf'));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	}
	
	
	public function editProfileInformationAction(Request $request){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			// Get the current user logged in
			$user = $this->get('security.context')->getToken()->getUser();
			
			// Get the department of the user
			$departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
			
			// Create a new formType with the needed variables
			$form = $this->createForm(new EditUserType($departmentId), $user);
			
			// Handle the form
			$form->handleRequest($request);
		
			// Check if the fields of the form is valid
			if ($form->isValid()) {
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();
				return $this->redirect($this->generateUrl('profile'));
			}
			
			return $this->render('profile/edit_profile.html.twig', array(
				'form' => $form->createView(),
				'user' => $user,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	}
	
	public function editProfilePasswordAction(Request $request){
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			// Get the current user logged in
			$user = $this->get('security.context')->getToken()->getUser();
			
			// Create a new formType with the needed variables
			$form = $this->createForm(new EditUserPasswordType(), $user);
			
			// Handle the form
			$form->handleRequest($request);
		
			// Check if the fields of the form is valid
			if ($form->isValid()) {
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();
				return $this->redirect($this->generateUrl('profile'));
			}
			
			return $this->render('profile/edit_profile_password.html.twig', array(
				'form' => $form->createView(),
				'user' => $user,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	
	}
	
	public function editProfileInformationAdminAction(Request $request){
		
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			// Get the ID sent by the request 
			$id = $this->getRequest()->get('id');
			
			// Find a user by the ID sent by the request
			$em = $this->getDoctrine()->getEntityManager();
			$user = $em->getRepository('AppBundle:User')->find($id);
			
			// Get the department of the user
			$departmentId = $user->getFieldOfStudy()->getDepartment()->getId();
			
			// Create a new formType with the needed variables
			$form = $this->createForm(new EditUserAdminType($departmentId), $user);
			
			// Handle the form
			$form->handleRequest($request);

			if ($form->isValid()) {
			
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();
				
				return $this->redirect($this->generateUrl('specific_profile', array('id' => $id )));
			}
			
			return $this->render('profile/edit_profile_admin.html.twig', array(
				'form' => $form->createView(),
				'user' => $user,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	
	}

    /**
     *
     * Updates profile photo for logged in user.
     * Request must contain a file of mime type image/gif, image/jpeg or image/png.
     * Moves the image file to the profile photos folder and updates the database.
     *
     * Note that no forms are created here, so it differs from the patterns used
     * elsewhere in this source file. See showEditProfilePhotoAction for rendered
     * page where user can upload photo. Reason: this function is also used by drag and drop
     * uploader in addition to the normal form with file selector. So I separated upload
     * handling and form rendering.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function editProfilePhotoAction(Request $request){
        //return new Response(var_dump($request->files));
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            //Target folder for the profile photo
            $targetFolder = $this->container->getParameter('profile_photos') . '/';
            //Create a FileUploader with target folder and allowed file types as parameters
            $uploader = new FileUploader($targetFolder, ['image/gif', 'image/jpeg', 'image/png']);
            //Move the file to target folder
            $result = $uploader->upload($request);
            //todo: if $result contains 0 or more than 1 entry, 0 or more than 1 image was uploaded to the target folder. Not sure if this can happen, but if it does something is wrong and must be handled.
            $path = $result[array_keys($result)[0]];
            //Update the database
            $user = $this->get('security.context')->getToken()->getUser();
            $user->setPicturePath($path);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($user);
            $em->flush();
            $id = $this->get('security.context')->getToken()->getUser()->getId();
            return $this->redirect($this->generateUrl('specific_profile', array('id' => $id )));
        }
        else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showEditProfilePhotoAction(){
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            // Get the current user logged in
            $user = $this->get('security.context')->getToken()->getUser();

            // Create the form
            $form = $this->createFormBuilder()
                ->setAction($this->generateUrl('profile_upload_photo'))
                ->setMethod('POST')
                ->add('profile_photo', 'file', array(
                    'label' => 'Profilbilde',
                ))
                ->add('save', 'submit', array(
                    'label' => 'Lagre',
                ))
                ->getForm();

            // Check if the fields of the form is valid
            //if ($form->isValid()) {
            //return new Response("Valid");
            //return $this->redirect($this->generateUrl('profile'));
            return $this->render('profile/edit_profile_photo.html.twig', array(
                'form' => $form->createView(),
                'user' => $user,
            ));
        }
        else {
            return $this->redirect($this->generateUrl('home'));
        }
    }
	
	
	public function showSpecificProfileAction(Request $request){
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			// Get the ID variable sent by the request
			$id = $this->getRequest()->get('id');
			
			$em = $this->getDoctrine()->getEntityManager();
			
			// Find the user with the ID equal to $id
			$user = $em->getRepository('AppBundle:User')->find($id);
			
			// Get the role of the user
			$roles = $user->getRoles();
					
			// Fetch the assistant history of the user
			$assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);
			
			// Find the work history of the user
			$workHistory =  $em->getRepository('AppBundle:WorkHistory')->findByUser($user);
			
			// If the user clicks their own public profile redirect them to their own profile site
			if ($user == $this->get('security.context')->getToken()->getUser()){
				return $this->redirect($this->generateUrl('profile'));
			}
			else {
				return $this->render('profile/public_profile.html.twig', array(
					'user' => $user,
					'assistantHistory' => $assistantHistory,
					'workHistory' => $workHistory,
					'roles' => $roles,
				));
			}
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
	}
	
	
    public function showAction(){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			// Get the user currently signed in
			$user = $this->get('security.context')->getToken()->getUser();
			
			// Get the ID of the user logged in
			$id = $user->getId();
			
			$em = $this->getDoctrine()->getEntityManager();
			
			// Get the role of the user
			$roles = $user->getRoles();
			
			// Fetch the user object with the user ID from database
			$user = $em->getRepository('AppBundle:User')->find($id);
			
			// Fetch the assistant history of the user
			$assistantHistory = $em->getRepository('AppBundle:AssistantHistory')->findByUser($user);
			
			// A array with all the active schools
			$activeSchools = array();
			
			// Find the work history of the user
			$workHistory =  $em->getRepository('AppBundle:WorkHistory')->findByUser($user);
			
			// Find any active assistant histories.. We use these to create a link on the profile page to the schools
			foreach ($assistantHistory as $as) {
				
				// Get the school of the assistant history
				$school = $as->getSchool();
				
				// Find active assistant histories
				$activeAssistantHistory = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesBySchool($school);
				
				if (in_array($as, $activeAssistantHistory)) {
					$activeSchools[] = $school;
				}
			}
			
			// Render the view
			return $this->render('profile/profile.html.twig', array(
				'user' => $user,
				'assistantHistory' => $assistantHistory,
				'activeSchools' => $activeSchools,
				'workHistory' => $workHistory,
				'roles' => $roles,
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
		
    }
	
}
