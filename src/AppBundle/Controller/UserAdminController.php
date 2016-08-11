<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\Type\CreateUserType;
use AppBundle\Form\Type\NewUserType;

class UserAdminController extends Controller
{
    public function createUserSuperadminAction(request $request)
    {

        // The the department ID parameter sent by the request
        $departmentId = $request->get('id');

        // Create the user object
        $user = new User();

        // This is sent to let the form know you are a superadmin
        $admin = 'superadmin';

        $form = $this->createForm(new CreateUserType($departmentId, $admin), $user, array(
            'validation_groups' => array('create_user'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {
            $role = $form->get('role')->getData();

            if ($role == 0) {
                $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole('ROLE_USER');
                $user->addRole($role);
            } elseif ($role == 1) {
                $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole('ROLE_ADMIN');
                $user->addRole($role);
            } elseif ($role == 2) {
                $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole('ROLE_SUPER_ADMIN');
                $user->addRole($role);
            }
            // In case something unexpected happens set default to ROLE_USER
            else {
                $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole('ROLE_USER');
                $user->addRole($role);
            }

            // Persist the user
            $em = $this->getDoctrine()->getManager();
            $user->setIsActive(1);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('useradmin_show'));
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', array(
             'form' => $form->createView(),
        ));
    }

    public function createUserAction(request $request)
    {
        $user = new User();

        // Get the department ID that the current user belongs to
        $departmentId = $this->get('security.context')->getToken()->getUser()->getFieldOfStudy()->getDepartment()->getId();

        // This is sent to let the form know you are an admin
        $admin = 'admin';

        // Create the form
        $form = $this->createForm(new CreateUserType($departmentId, $admin), $user, array(
            'validation_groups' => array('create_user'),
        ));

        // Handle the form
        $form->handleRequest($request);

        // The fields of the form is checked if they contain the correct information
        if ($form->isValid()) {

            // Set access rights equal to user by default.
            $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneByRole('ROLE_USER');
            $user->addRole($role);

            // If valid insert into database
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('useradmin_show'));
        }

        // Render the view
        return $this->render('user_admin/create_user.html.twig', array(
             'form' => $form->createView(),
        ));
    }

    public function showAction()
    {

        // Only show for ROLE_ADMIN and upwards
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Finds the department for the current logged in user
            $department = $this->get('security.token_storage')->getToken()->getUser()->getFieldOfStudy()->getDepartment();

            // Finds the users for the given department
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($department);

            return $this->render('user_admin/index.html.twig', array(
                'users' => $users,
                'departments' => $allDepartments,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function showUsersByDepartmentAction($id)
    {

        // Only show for ROLE_SUPER_ADMIN and upwards
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Finds all the departments
            $allDepartments = $this->getDoctrine()->getRepository('AppBundle:Department')->findAll();

            // Finds the  users of the departmend with the departmed id of $id
            $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAllUsersByDepartment($id);

            // Renders the view with the variables
            return $this->render('user_admin/index.html.twig', array(
                'users' => $users,
                'departments' => $allDepartments,
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function deleteUserByIdAction($id)
    {
        try {
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given user
                $em = $this->getDoctrine()->getEntityManager();
                $userToBeDeleted = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
                $em->remove($userToBeDeleted);
                $em->flush();

                $response['success'] = true;
            }
            // You have to check for admin rights here to prevent admins from deleting users that are not in their department
            elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $em = $this->getDoctrine()->getEntityManager();
                // Find a user by a given ID 
                $userToBeDeleted = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
                // Find the department of the user that is being deleted
                $department = $userToBeDeleted->getFieldOfStudy()->getDepartment();

                // Is the admin from the same department as the user he is trying to delete?
                if ($this->get('security.context')->getToken()->getUser()->getFieldOfStudy()->getDepartment() === $department) {
                    $em->remove($userToBeDeleted);
                    $em->flush();
                    // Send a response back to AJAX
                    $response['success'] = true;
                } else {
                    // Send a response back to AJAX
                    $response['success'] = false;
                    $response['cause'] = 'Du kan ikke slette en bruker som ikke er fra din avdeling.';
                }
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            return new JsonResponse([
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det er ikke mulig å slette brukeren. Vennligst kontakt IT-ansvarlig.',
                // 'cause' => $e->getMessage(), if you want to see the exception message. 
            ]);
        }

        // Send a respons to ajax 
        return new JsonResponse($response);
    }

    /**
     * Sets newUserCode and sends activation email to user.
     *
     * @param User user
     */
    public function sendActivationEmail(User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $createNewUserCode = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedNewUserCode = hash('sha512', $createNewUserCode, false);
        $user->setNewUserCode($hashedNewUserCode);
        $em->persist($user);
        $em->flush();

        $emailMessage = \Swift_Message::newInstance()
            ->setSubject('Velkommen til vektorprogrammet')
            ->setFrom($this->container->getParameter('no_reply_email_user_creation'))
            ->setTo($user->getEmail())
            ->setBody($this->renderView('new_user/create_new_user_email.txt.twig', array('newUserURL' => $this->generateURL('useradmin_activate_user', array('newUserCode' => $createNewUserCode), true))));
        $this->get('mailer')->send($emailMessage);
    }

    public function activateNewUserAction(Request $request, $newUserCode)
    {
        try {
            $repositoryUser = $this->getDoctrine()->getRepository('AppBundle:User');
            $hashedNewUserCode = hash('sha512', $newUserCode, false);
            $user = $repositoryUser->findUserByNewUserCode($hashedNewUserCode);

            $form = $this->createForm(new NewUserType(), $user);

            $form->handleRequest($request);

            //Checks if the form is valid
            if ($form->isValid()) {
                //Deletes the newUserCode, so it can only be used one time.
                $user->setNewUserCode(null);

                $user->setIsActive('1');

                //Updates the database
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                //renders the login page, with a feedback message so that the user knows that the new password was stored.
                $feedback = 'Logg inn med din nye bruker';

                return $this->render('Login/login.html.twig', array('message' => $feedback, 'error' => null, 'last_username' => $user->getUsername()));
            }
            //Render reset_password twig with the form.
            return $this->render('new_user/create_new_user.html.twig', array('form' => $form->createView(), 'firstName' => $user->getFirstName(), 'lastName' => $user->getLastName()));
        } catch (\Exception $e) {
            return $this->redirect('/');
        }
    }
}
