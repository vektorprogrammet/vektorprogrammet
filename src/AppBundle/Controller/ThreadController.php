<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Thread;
use AppBundle\Form\Type\CreateThreadType;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class ThreadController extends Controller
{
    public function deleteThreadAction(Request $request)
    {

        // Get the ID sen by the request
        $id = $request->get('id');

        $em = $this->getDoctrine()->getEntityManager();

        // Empty thread entity
        $thread = new Thread();

        try {
            // Only SUPER_ADMIN can delete forums
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // Find the thread with the given ID
                $thread = $this->getDoctrine()->getRepository('AppBundle:Thread')->find($id);
            } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

                // Find the current user
                $user = $this->get('security.context')->getToken()->getUser();

                // Find the thread with the given ID
                $thread = $this->getDoctrine()->getRepository('AppBundle:Thread')->find($id);

                // Check if the current user is the one who made the thread
                if (!($thread->getUser() == $user)) {
                    return $this->redirect($this->generateUrl('forum_show'));
                }
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }

            // This deletes the given thread
            $em->remove($thread);
            $em->flush();

            // Send a response back to AJAX
            $response['success'] = true;
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette traaden.';
            //$response['cause'] = $e->getMessage(); // if you want to see the exception message.

            return new JsonResponse($response);
        }

        // Send a respons to ajax
        return new JsonResponse($response);
    }

    public function editThreadAction(Request $request)
    {

        // Get the ID and forumdID variable from the request
        $id = $request->get('id');
        $forumId = $request->get('forumId');

        $em = $this->getDoctrine()->getManager();

        // Create a new thread entity
        $thread = new Thread();

        // Only SUPER_ADMIN can edit all threads
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Find a thread by the ID sent in by the request
            $thread = $em->getRepository('AppBundle:Thread')->find($id);
        } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Find the current user
            $user = $this->get('security.context')->getToken()->getUser();

            // Find a thread by the ID sent in by the request
            $thread = $em->getRepository('AppBundle:Thread')->find($id);

            // Check if the current user is the one who made the thread
            if ($thread->getUser() == $user) {
            } else {
                return $this->redirect($this->generateUrl('forum_show'));
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }

        // Create the form
        $form = $this->createForm(new CreateThreadType(), $thread);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isValid()) {
            // Persist the changes
            $em->persist($thread);
            $em->flush();

            return $this->redirect($this->generateUrl('forum_show_specific_thread_by_id',  array('id' => $thread->getId(), 'forumId' => $forumId)));
        }

        // Return the view
        return $this->render('forum/create_thread.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    // Shows the threads of a specific subforum
    public function showSpecificSubforumThreadsAction(Request $request)
    {

        // Get the variable sent by the request
        $id = $request->get('id');
        // We need the forumId becuase the relation between forum and subforums are many-to-many
        // To navigate back to the correct subforum, we have to keep track of the forumId
        $forumId = $request->get('forumId');

        // Only ROLE_SUPER_ADMIN and above can see all subforum threads
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Find the subforum object associated with the ID
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);

            // Find the threads that are associated with the subforum
            $threads = $this->getDoctrine()->getRepository('AppBundle:Thread')->findBySubforum($subforum);

            // Find a forum with the given ID
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);
        } elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {

            // Find the subforum object associated with the ID
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);

            // Find the current user
            $user = $this->get('security.context')->getToken()->getUser();

            // Find a forum with the given ID
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);

            // Find all the active work histories of the user
            $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);

            // Boolean to keep track if the user is in a valid subforum
            $valid = false;

            if ($subforum->getType() == 'school' || $subforum->getType() == 'general') {
                $valid = true;
            } else {

                // Check each active assistant histories
                foreach ($activeWorkHistories as $awh) {

                    // Get the teams associated with the subforum
                    $teams = $subforum->getTeams();

                    // Loop through each team
                    foreach ($teams as $team) {
                        if ($team == $awh->getTeam()) {
                            $valid = true;
                        }
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            } else {
                // Find the threads that are associated with the subforum
                $threads = $this->getDoctrine()->getRepository('AppBundle:Thread')->findBySubforum($subforum);
            }
        } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Find the subforum object associated with the ID
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);

            // Find the current user
            $user = $this->get('security.context')->getToken()->getUser();

            // Find a forum with the given ID
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);

            // Find all the active assistant histories of the user
            $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);

            // Boolean to keep track if the user is in a valid subforum
            $valid = false;

            if ($subforum->getType() == 'general') {
                $valid = true;
            } else {
                // Check each active assistant histories
                foreach ($activeAssistantHistories as $ash) {

                    // Get the schools associated with the subforum
                    $schools = $subforum->getSchools();

                    // Loop through each schools
                    foreach ($schools as $school) {
                        if ($school == $ash->getSchool()) {
                            $valid = true;
                        }
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            } else {
                // Find the threads that are associated with the subforum
                $threads = $this->getDoctrine()->getRepository('AppBundle:Thread')->findBySubforum($subforum);
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }

        // Return the view
        return $this->render('forum/specific_subforum.html.twig', array(
            'threads' => $threads,
            'subforum' => $subforum,
            'forum' => $forum,
        ));
    }

    // Creates a new thread in a specific subforum
    public function createThreadAction(Request $request)
    {

        // Get the variable sent by the request
        $id = $request->get('id');

        // Only ROLE_SUPER_ADMIN can make threads for every subforum
        if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Create a new post entity
            $thread = new Thread();

            // Find the subforum with the given ID sent by the request
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);
        }
        // ROLE_ADMIN can only make threads in team forums they are associated with, but all school forums
        elseif ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {

            // Create a new post entity
            $thread = new Thread();

            // Find the subforum with the given ID sent by the request
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);

            // Find the current user
            $user = $this->get('security.context')->getToken()->getUser();

            // Find all the active work histories of the user
            $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);

            // Boolean to keep track if the user is in a valid subforum
            $valid = false;

            // Check each active assistant histories
            foreach ($activeWorkHistories as $awh) {

                // Check if the subforum is a geneal one
                if ($subforum->getType() == 'general' || $subforum->getType() == 'school') {
                    $valid = true;
                } else {
                    // Get the teams associated with the subforum
                    $teams = $subforum->getTeams();

                    // Loop through each team
                    foreach ($teams as $team) {
                        if ($team == $awh->getTeam()) {
                            $valid = true;
                        }
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            }
        }
        // ROLE_USER can only make threads in forums they are associated with
        elseif ($this->container->get('security.context')->isGranted('ROLE_USER')) {

            // Create a new post entity
            $thread = new Thread();

            // Find the subforum with the given ID sent by the request
            $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->findOneById($id);

            // Find the current user
            $user = $this->get('security.context')->getToken()->getUser();

            // Find all the active assistant histories of the user
            $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);

            // Boolean to keep track if the user is in a valid subforum
            $valid = false;

            if ($subforum->getType() == 'general') {
                $valid = true;
            }

            // Check each active assistant histories
            foreach ($activeAssistantHistories as $ash) {

                // Get the schools associated with the subforum
                $schools = $subforum->getSchools();

                // Loop through each schools
                foreach ($schools as $school) {
                    if ($school == $ash->getSchool()) {
                        $valid = true;
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }

        // Create the form
        $form = $this->createForm(new CreateThreadType(), $thread);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Get the current user.
            $user = $this->get('security.context')->getToken()->getUser();

            // Create a new DateTime object
            $now = new DateTime('now');

            // Set necessary variables for the post entity
            $thread->setUser($user);
            $thread->setDateTime($now);
            $thread->setSubforum($subforum);

            $em->persist($thread);
            $em->flush();

            // Redirect to the proper subforum
            return $this->redirect($this->generateUrl('forum_show_specific_thread_by_id', array('id' => $thread->getId(), 'forumId' => $request->get('forumId'))));
        }

        // Return the view
        return $this->render('forum/create_thread.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
