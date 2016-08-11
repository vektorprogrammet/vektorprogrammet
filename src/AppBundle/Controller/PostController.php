<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Post;
use AppBundle\Form\Type\CreatePostType;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    public function editPostAction(Request $request)
    {

        // Get the ID variable from the request
        $id = $request->get('id');

        $forumId = $request->get('forumId');

        $em = $this->getDoctrine()->getManager();

        // Create a new post entity
        $post = new Post();

        // Only SUPER_ADMIN can edit all threads 
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Find a post by the ID sent in by the request 
            $post = $em->getRepository('AppBundle:Post')->find($id);
        } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Find the current user 
            $user = $this->get('security.context')->getToken()->getUser();

            // Find a post by the ID sent in by the request 
            $post = $em->getRepository('AppBundle:Post')->find($id);

            // Check if the current user is the one who made the post
            if (!($post->getUser() == $user)) {
                return $this->redirect($this->generateUrl('forum_show'));
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }

        // Create the form
        $form = $this->createForm(new CreatePostType(), $post);

        // Handle the form
        $form->handleRequest($request);

        // Check if the form is valid 
        if ($form->isValid()) {
            // Persist the changes 
            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('forum_show_specific_thread_by_id',  array('id' => $post->getThread()->getId(), 'forumId' => $forumId)));
        }

        // Return the view
        return $this->render('forum/create_post.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deletePostAction(Request $request)
    {

        // Get the ID sen by the request 
        $id = $request->get('id');

        $em = $this->getDoctrine()->getEntityManager();

        // Empty post entity
        $post = new Post();

        try {
            // Only SUPER_ADMIN can delete all post
            if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

                // Find the post with the given ID 
                $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);
            }
            // Users can only delete their own posts 
            elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

                // Find the current user 
                $user = $this->get('security.context')->getToken()->getUser();

                // Find the post with the given ID 
                $post = $this->getDoctrine()->getRepository('AppBundle:Post')->find($id);

                // Check if the current user is the one who made the post
                if (!($post->getUser() == $user)) {
                    return $this->redirect($this->generateUrl('forum_show_specific_thread_by_id',  array('id' => $post->getThread->getId())));
                }
            } else {
                // Send a response back to AJAX
                $response['success'] = false;
                $response['cause'] = 'Du har ikke tilstrekkelige rettigheter.';
            }

            // This deletes the given thread
            $em->remove($post);
            $em->flush();

            // Send a response back to AJAX
            $response['success'] = true;
        } catch (\Exception $e) {
            // Send a response back to AJAX
            $response['success'] = false;
            $response['cause'] = 'Kunne ikke slette innlegget.';
            //$response['cause'] = $e->getMessage(); // if you want to see the exception message. 

            return new JsonResponse($response);
        }

        // Send a respons to ajax 
        return new JsonResponse($response);
    }

    // Show the specifc thread and its posts
    public function showSpecificThreadAction(Request $request)
    {

        // Get the variable sent by the request 
        $id = $request->get('id');

        // We need the forumId becuase the relation between forum and subforums are many-to-many
        // To navigate back to the correct subforum, we have to keep track of the forumId
        $forumId = $request->get('forumId');

        // Only ROLE_SUPER_ADMIN and above can see all threads
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {

            // Find the thread entity
            $thread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findOneById($id);

            // Find all the posts associated with the thread
            $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findByThread($thread);

            // Find a forum with the given ID 
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);
        }
        // ROLE_ADMIN can see all general and school threads, but only team threads that are associated with the teams the user is associated with
        elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {

            // Find the thread entity
            $thread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findOneById($id);

            // Find a forum with the given ID 
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);

            // Find the current user 
            $user = $this->get('security.context')->getToken()->getUser();

            // Find all the active work histories of the user 
            $activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);

            // Find the subforum of the thread 
            $subforum = $thread->getSubforum();

            // Boolean to keep track if the user is visting a subforum that he/she is allowed to visit 
            $valid = false;

            // If the subforum of the thread is not general or school we have to check if the user is associated with this subforum
            if (!($subforum->getType() == 'school' || $subforum->getType() == 'general')) {

                // Check each active work histories 
                foreach ($activeWorkHistories as $awh) {

                    // Find the teams associated with the subforum
                    $teams = $subforum->getTeams();

                    // Loop through each team
                    foreach ($teams as $t) {

                        // Check if the user is actively working at that team
                        if ($awh->getTeam() == $t) {
                            $valid = true;
                        }
                    }
                }
            }
            // Set valid if the subforum is general or school type
            elseif ($subforum->getType() == 'general' || $subforum->getType() == 'school') {
                $valid = true;
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            } else {
                // Find all the posts associated with the thread
                $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findByThread($thread);
            }
        } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {

            // Find the thread entity
            $thread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findOneById($id);

            // Find a forum with the given ID 
            $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($forumId);

            // Find the current user 
            $user = $this->get('security.context')->getToken()->getUser();

            // Find all the active assistant histories of the user 
            $activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);

            // Find the subforum of the thread 
            $subforum = $thread->getSubforum();

            // Boolean to keep track if the user is visting a subforum that he/she is allowed to visit 
            $valid = false;

            if ($subforum->getType() == 'general') {
                $valid = true;
            } else {

                // Check each active assistant histories 
                foreach ($activeAssistantHistories as $ash) {

                    // Find the schools associated with the subforum
                    $schools = $subforum->getSchools();

                    // Loop through each school
                    foreach ($schools as $s) {

                        // Check if the user is actively working at that school
                        if ($ash->getSchool() == $s) {
                            $valid = true;
                        }
                    }
                }
            }

            if ($valid == false) {
                return $this->redirect($this->generateUrl('home'));
            } else {
                // Find all the posts associated with the thread
                $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findByThread($thread);
            }
        } else {
            return $this->redirect($this->generateUrl('home'));
        }

        // Create a new post entity
        $post = new Post();

        // Create the form 
        $form = $this->createForm(new CreatePostType(), $post);

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
            $post->setUser($user);
            $post->setDateTime($now);
            $post->setThread($thread);

            $em->persist($post);
            $em->flush();

            return $this->redirect($this->generateUrl('forum_show_specific_thread_by_id', array('id' => $request->get('id'), 'forumId' => $request->get('forumId'))));
        }

        // Return the view
        return $this->render('forum/specific_thread.html.twig', array(
            'thread' => $thread,
            'posts' => $posts,
            'form' => $form->createView(),
            'forum' => $forum,
        ));
    }
}
