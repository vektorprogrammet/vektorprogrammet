<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Subforum;
use AppBundle\Form\Type\CreateSubforumType;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubforumController extends Controller
{
    public function editSubforumAction(Request $request)
    {

        // Only edit if it is a SUPER_ADMIN
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            // Get the ID variable from the request
            $id = $request->get('id');

            // Create a new forum entity
            $subforum = new Subforum();

            // find the parent forum of the subforum

            $em = $this->getDoctrine()->getManager();

            // Find a forum by the ID sent in by the request 
            $subforum = $em->getRepository('AppBundle:Subforum')->find($id);

            // Create the form
            $form = $this->createForm(new CreateSubforumType(), $subforum);

            // Handle the form
            $form->handleRequest($request);

            // Check if the form is valid 
            if ($form->isValid()) {
                // Persist the changes 
                $em->persist($subforum);
                $em->flush();

                return $this->redirect($this->generateUrl('forum_show'));
            }

            // Return the view
            return $this->render('forum/create_subforum.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    public function deleteSubforumAction(Request $request)
    {

        // Get the ID sen by the request 
        $id = $request->get('id');

        try {
            // Only SUPER_ADMIN can delete forums
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // This deletes the given forum
                $em = $this->getDoctrine()->getEntityManager();
                $subforum = $this->getDoctrine()->getRepository('AppBundle:Subforum')->find($id);

                $em->remove($subforum);
                $em->flush();

                // Send a response back to AJAX
                $response['success'] = true;
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            //$response['cause'] = 'Kunne ikke slette subforumet.';
            $response['cause'] = $e->getMessage(); // if you want to see the exception message. 

            return new JsonResponse($response);
        }

        // Send a respons to ajax 
        return new JsonResponse($response);
    }

    // Creates a subforum 
    public function createSubforumAction(Request $request)
    {

        // Only ROLE_SUPER_ADMIN can create new subforums 
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // A new forum entity 
            $subforum = new Subforum();

            // Create the form 
            $form = $this->createForm(new CreateSubforumType(), $subforum);

            // Handle the form 
            $form->handleRequest($request);

            // Check if the form is valid
            if ($form->isValid()) {

                // Check whether the checkbox was checked or not 
                if ($form['type']->getData() == 'general') {

                    // Find all general forums
                    $forums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findByType('general');

                    // Add the subforum to all the general forums
                    foreach ($forums as $forum) {
                        $subforum->addForum($forum);
                        $forum->addSubforum($subforum);
                    }
                }

                // List of schools
                $schools = array();

                // List of teams 
                $teams = array();

                // Add schools to the schools list
                foreach ($form['schools']->getData() as $school) {
                    $schools[] = $school;
                }

                // Add teams to the teams list
                foreach ($form['teams']->getData() as $team) {
                    $teams[] = $team;
                }

                if (!(empty($schools))) {
                    // Find all the school forums
                    $forums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findByType('school');

                    // Add the subforum to all the school forums
                    foreach ($forums as $forum) {
                        $subforum->addForum($forum);
                        $forum->addSubforum($subforum);
                    }
                }

                if (!(empty($teams))) {
                    // Find all the school forums
                    $forums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findByType('team');

                    // Add the subforum to all the school forums
                    foreach ($forums as $forum) {
                        $subforum->addForum($forum);
                        $forum->addSubforum($subforum);
                    }
                }

                // Store the forum in the database 
                $em = $this->getDoctrine()->getManager();
                $em->persist($subforum);
                $em->flush();

                // Redirect to the proper subforum 
                return $this->redirect($this->generateUrl('forum_show'));
            }

            // Return the view
            return $this->render('forum/create_subforum.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirect($this->generateUrl('home'));
        }
    }

    // Shows a specific forum
    public function showSpecificAction(Request $request)
    {
        $id = $request->get('id');

        // Array to keep track of latest activity
        $latestActivity = array();

        // Only ROLE_ADMIN and above can see all subforums
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Find the forum by ID, sent by the request
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($id);

            // Find all the subforums of the given forum
            $validSubforums = $forum->getSubforums();

            foreach ($validSubforums as $subforum) {

                // Initate variables 
                $post = null;
                $thread = null;

                // Find the latest post for the given subforum 
                $latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostBySubforum($subforum);

                // Find the latest thread for the given subforum 
                $latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadBySubforum($subforum);

                // The methods findLatestThreadBySubforum and findLatestPostBySubforum returns an array
                // We have to loop through the array even if it just a single element in it 

                foreach ($latestPost as $lp) {
                    $post = $lp;
                }
                foreach ($latestThread as $lt) {
                    $thread = $lt;
                }

                // Check if both are null, this means there are no threads and posts 
                if (!($post == null) || !($thread == null)) {
                    // Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() >= $thread->getDateTime()) {
                            // Add post to the array 
                            $latestActivity[$subforum->getId()] = $post;
                        }
                    }
                    // Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null)) && (!($post == null))) {
                            // Add thread to the array 
                            $latestActivity[$subforum->getId()] = $thread;
                        }
                    }
                    // If the latestPost array is empty, we add the thread
                    elseif (($post == null) && (!($thread == null))) {
                        // Add thread to the array 
                        $latestActivity[$subforum->getId()] = $thread;
                    }
                    // If the latestThread array is empty, we add the post
                    elseif (($thread == null) && (!($post == null))) {
                        // Add post to the array 
                        $latestActivity[$subforum->getId()] = $post;
                    }
                }
            }
        } elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {

            // Find the current user 
            $user = $this->get('security.context')->getToken()->getUser();

            // Find the forum by ID, sent by the request
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($id);

            // Find all the subforums of the given forum
            $allSubforums = $forum->getSubforums();

            // Find all the active work histories of the user 
            $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);

            // empty array, should contain the subforums the user is allowed to access 
            $validSubforums = array();

            // Loop through all the subforums 
            foreach ($allSubforums as $sf) {
                // Check if the subforum is general 
                if ($sf->getType() == 'general' || $sf->getType() == 'school') {
                    $validSubforums[] = $sf;
                }
            }

            // Check each active work histories 
            foreach ($activeWorkHistories as $awh) {

                // Loop through all the subforums 
                foreach ($allSubforums as $sf) {

                    // Find the teams associated with the subforum
                    $teams = $sf->getTeams();

                    // Loop through each team
                    foreach ($teams as $t) {

                        // Check if the user is actively working at that team
                        if ($awh->getTeam() == $t) {

                            // Check if the subforum is already added
                            if (!(in_array($sf, $validSubforums))) {
                                $validSubforums[] = $sf;
                            }
                        }
                    }
                }
            }

            foreach ($validSubforums as $subforum) {

                // Initate variables 
                $post = null;
                $thread = null;

                // Find the latest post for the given subforum 
                $latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostBySubforum($subforum);

                // Find the latest thread for the given subforum 
                $latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadBySubforum($subforum);

                // The methods findLatestThreadBySubforum and findLatestPostBySubforum returns an array
                // We have to loop through the array even if it just a single element in it 

                foreach ($latestPost as $lp) {
                    $post = $lp;
                }
                foreach ($latestThread as $lt) {
                    $thread = $lt;
                }

                // Check if both are null, this means there are no threads and posts 
                if (!($post == null) || !($thread == null)) {
                    // Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() >= $thread->getDateTime()) {
                            // Add post to the array 
                            $latestActivity[$subforum->getId()] = $post;
                        }
                    }
                    // Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null)) && (!($post == null))) {
                            // Add thread to the array 
                            $latestActivity[$subforum->getId()] = $thread;
                        }
                    }
                    // If the latestPost array is empty, we add the thread
                    elseif (($post == null) && (!($thread == null))) {
                        // Add thread to the array 
                        $latestActivity[$subforum->getId()] = $thread;
                    }
                    // If the latestThread array is empty, we add the post
                    elseif (($thread == null) && (!($post == null))) {
                        // Add post to the array 
                        $latestActivity[$subforum->getId()] = $post;
                    }
                }
            }

            // If a valid subforum does not exist the user is in the wrong forum, which he/she should not have access to. 

            // Keep track if the user is in a valid forum
            $valid = false;

            // Loop through each valid forum
            foreach ($validSubforums as $vsf) {

                // Loop through each subforum of this specific forum with a ID given by the request
                foreach ($allSubforums as $asf) {

                    // If a match exist we set the forum to be valid 
                    if ($vsf == $asf) {
                        $valid = true;
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            }
        } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Find the current user 
            $user = $this->get('security.context')->getToken()->getUser();

            // Find the forum by ID, sent by the request
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($id);

            // Find all the subforums of the given forum
            $allSubforums = $forum->getSubforums();

            // Find all the active assistant histories of the user 
            $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);

            // empty array, should contain the subforums the user is allowed to access 
            $validSubforums = array();

            // Loop through all the subforums 
            foreach ($allSubforums as $sf) {
                // Check if the subforum is general 
                if ($sf->getType() == 'general') {
                    $validSubforums[] = $sf;
                }
            }

            // Check each active assistant histories 
            foreach ($activeAssistantHistories as $ash) {

                // Loop through all the subforums 
                foreach ($allSubforums as $sf) {

                    // Find the schools associated with the subforum
                    $schools = $sf->getSchools();

                    // Loop through each school
                    foreach ($schools as $s) {

                        // Check if the user is actively working at that school
                        if ($ash->getSchool() == $s) {
                            // Check if the subforum is already added
                            if (!(in_array($sf, $validSubforums))) {
                                $validSubforums[] = $sf;
                            }
                        }
                    }
                }
            }

            foreach ($validSubforums as $subforum) {

                // Initate variables 
                $post = null;
                $thread = null;

                // Find the latest post for the given subforum 
                $latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostBySubforum($subforum);

                // Find the latest thread for the given subforum 
                $latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadBySubforum($subforum);

                // The methods findLatestThreadBySubforum and findLatestPostBySubforum returns an array
                // We have to loop through the array even if it just a single element in it 

                foreach ($latestPost as $lp) {
                    $post = $lp;
                }
                foreach ($latestThread as $lt) {
                    $thread = $lt;
                }

                // Check if both are null, this means there are no threads and posts 
                if (!($post == null) || !($thread == null)) {
                    // Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() >= $thread->getDateTime()) {
                            // Add post to the array 
                            $latestActivity[$subforum->getId()] = $post;
                        }
                    }
                    // Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
                    if ((!($thread == null)) && (!($post == null))) {
                        if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null)) && (!($post == null))) {
                            // Add thread to the array 
                            $latestActivity[$subforum->getId()] = $thread;
                        }
                    }
                    // If the latestPost array is empty, we add the thread
                    elseif (($post == null) && (!($thread == null))) {
                        // Add thread to the array 
                        $latestActivity[$subforum->getId()] = $thread;
                    }
                    // If the latestThread array is empty, we add the post
                    elseif (($thread == null) && (!($post == null))) {
                        // Add post to the array 
                        $latestActivity[$subforum->getId()] = $post;
                    }
                }
            }

            // If a valid subforum does not exist the user is in the wrong forum, which he/she should not have access to. 

            // Keep track if the user is in a valid forum
            $valid = false;

            // Loop through each valid forum
            foreach ($validSubforums as $vsf) {

                // Loop through each subforum of this specific forum with a ID given by the request
                foreach ($allSubforums as $asf) {

                    // If a match exist we set the forum to be valid 
                    if ($vsf == $asf) {
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

        // Return the view
        return $this->render('forum/sub_forums.html.twig', array(
            'subforums' => $validSubforums,
            'latestActivity' => $latestActivity,
            'forum' => $forum,
        ));
    }
}
