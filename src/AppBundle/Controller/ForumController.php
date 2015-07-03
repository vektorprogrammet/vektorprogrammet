<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use \DateTime;
use AppBundle\Entity\Forum;
use AppBundle\Form\Type\CreateForumType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ForumController extends Controller {
	
	public function editForumAction(Request $request){
	
		// Only edit if it is a SUPER_ADMIN
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			// Get the ID variable from the request
			$id = $request->get('id');
			
			// Create a new forum entity
			$forum = new Forum();
			
			$em = $this->getDoctrine()->getManager();
			
			// Find a forum by the ID sent in by the request 
			$forum = $em->getRepository('AppBundle:Forum')->find($id);
				
			// Create the form
			$form = $this->createForm(new CreateForumType(), $forum);
			
			// Handle the form
			$form->handleRequest($request);
			
			// Check if the form is valid 
			if ($form->isValid()) {
				// Persist the changes 
				$em->persist($forum);
				$em->flush();
				return $this->redirect($this->generateUrl('forum_show'));
			}
				
			// Return the view
			return $this->render('forum/create_forum.html.twig', array(
				'form' => $form->createView(),
			));
		}
		else{
			return $this->redirect($this->generateUrl('home'));
		}
		
	}
	
	public function deleteForumAction(Request $request){
		
		// Get the ID sent by the request 
		$id = $request->get('id');
	
		try {
			// Only SUPER_ADMIN can delete forums
			if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
				// This deletes the given forum
				$em = $this->getDoctrine()->getEntityManager();
				$forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->find($id);
				
				$em->remove($forum);
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
			$response['cause'] = 'Kunne ikke slette forumet.';
			//$response['cause'] = $e->getMessage(); // if you want to see the exception message. 
			
			return new JsonResponse( $response );
		}
		
		// Send a respons to ajax 
		return new JsonResponse( $response );
	
	
	}
	
	
	public function createForumAction(Request $request){
		
		// Only ROLE_SUPER_ADMIN can create new forums 
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
			
			// A new forum entity 
			$forum = new Forum();
			
			// Create the form 
			$form = $this->createForm(new CreateForumType(), $forum);
				
			// Handle the form 
			$form->handleRequest($request);
			
			// Check if the form is valid
			if ($form->isValid()) {
				
			
				// Store the forum in the database 
				$em = $this->getDoctrine()->getManager();
				$em->persist($forum);
				$em->flush();
					
				// Redirect to the proper subforum 
				return $this->redirect($this->generateUrl('forum_show'));
			}
			
			// Return the view
			return $this->render('forum/create_forum.html.twig', array(
				'form' => $form->createView(),
			));
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}	
	}
	
	
	// Renders the first page when you click on the forum button
    public function showAction(){	
	
		// Array to keep track of latest activity
		$latestActivity = array();
		
		// Only ROLE_ADMIN and above can see all forums
		if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
		
			// Get all forums
			$forums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findAll();
			
			// Loop through each forum to find the latest activity 
			foreach($forums as $forum) {
				
				// Initate variables 
				$post = null;
				$thread = null;
				
				// Find the latest post for the given forum 
				$latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostByForum($forum);
				
				// Find the latest thread for the given forum 
				$latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadByForum($forum);
					
				// The methods findLatestThreadByForum and findLatestPostByForum returns an array
				// We have to loop through the array even if it just a single element in it 	
				foreach($latestPost as $lp){
					$post = $lp;
				}
				foreach($latestThread as $lt){
					$thread = $lt;
				}
				
				// Check if both are null, this means there are no threads and posts 
				if ( !($post == null) || !($thread == null)) {
					// Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
					if ((!($thread == null )) && (!($post == null))) {
						if ($post->getDateTime() >= $thread->getDateTime() ){        
							// Add post to the array 
							$latestActivity[$forum->getId()] = $post;
						}
					}
					// Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
					if ((!($thread == null )) && (!($post == null))) {
						if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null )) && (!($post == null))  ){
							// Add thread to the array 
							$latestActivity[$forum->getId()] = $thread;
						}
					}
					// If the latestPost array is empty, we add the thread
					elseif ( ($post == null ) && (!($thread == null)) ) {
						// Add thread to the array 
						$latestActivity[$forum->getId()] = $thread;
					}
					// If the latestThread array is empty, we add the post
					elseif ( ($thread == null ) && (!($post == null )) ){
						// Add post to the array 
						$latestActivity[$forum->getId()] = $post;
					}
				}

			}
				
		}

		elseif ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
		
			// Find the current user 
			$user= $this->get('security.context')->getToken()->getUser();
				
			// Find all the active work histories of the user 
			$activeWorkHistories = $this->getDoctrine()->getRepository('AppBundle:WorkHistory')->findActiveWorkHistoriesByUser($user);	
			
			// Find all forums
			$allForums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findAll();
			
			// empty array, should contain the forums the user is allowed to access 
			$forums = array();
			
			foreach ($allForums as $forum){
				// If it is a general forum we add it to the array
				if ( $forum->getType() == "general" || $forum->getType() == "school" ){
					$forums[] = $forum;
				}
			}
			// Check each active work histories 
			foreach ($activeWorkHistories as $awh){
				// Loop through each forum
				foreach ($allForums as $forum){
					
					// Find all the subforums of the forum
					$subforums = $forum->getSubforums();
						
					// Loop through all the subforums 
					foreach($subforums as $sf){
							
						// Find the teams associated with the subforum
						$teams = $sf->getTeams();
							
						// Loop through each team
						foreach ($teams as $t){
								
							// Check if the user is actively working at that team
							if ($awh->getTeam() == $t){
								
								// Check if the forum is already added
								if(!(in_array($forum, $forums))){
										$forums[] = $forum;
								}
									
							}		
						}
					}
				}
			}		
		
			
			// Loop through each forum the user is allowed to visit 
			foreach($forums as $forum) {
				
				// Initate variables 
				$post = null;
				$thread = null;
				
				// If the forum type is school or general we can just find the latest posts and threads 
				if ( $forum->getType() == "general" || $forum->getType() == "school" ){
						
					// Find the latest post for the given forum 
					$latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostByForum($forum);
							
					// Find the latest thread for the given forum 
					$latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadByForum($forum);
								
					// The methods findLatestThreadByForum and findLatestPostByForum returns an array
					// We have to loop through the array even if it just a single element in it 	
					foreach($latestPost as $lp){
						$post = $lp;
					}
					foreach($latestThread as $lt){
						$thread = $lt;
					}
					
					// Check if both are null, this means there are no threads and posts 
					if ( !($post == null) || !($thread == null)) {
						// Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
						if ((!($thread == null )) && (!($post == null))) {
							if ($post->getDateTime() >= $thread->getDateTime() ){        
								// Add post to the array 
								$latestActivity[$forum->getId()] = $post;
							}
						}
						// Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
						if ((!($thread == null )) && (!($post == null))) {
							if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null )) && (!($post == null))  ){
								// Add thread to the array 
								$latestActivity[$forum->getId()] = $thread;
							}
						}
						// If the latestPost array is empty, we add the thread
						elseif ( ($post == null ) && (!($thread == null)) ) {
							// Add thread to the array 
							$latestActivity[$forum->getId()] = $thread;
						}
						// If the latestThread array is empty, we add the post
						elseif ( ($thread == null ) && (!($post == null )) ){
							// Add post to the array 
							$latestActivity[$forum->getId()] = $post;
						}
					}
				}
				// If the forum is not general we have to check whether the user is associated with the forum through activeWorkHistories 
				else {
					foreach ($activeWorkHistories as $awh){
						// Find all the subforums of the forum
						$subforums = $forum->getSubforums();
									
						// Loop through all the subforums 
						foreach($subforums as $subforum){
							
							// Find the teams associated with the subforum
							$teams = $subforum->getTeams();
								
							// Loop through each school
							foreach ($teams as $t){

								// Check if the user is actively working at that school
								if ($awh->getTeam() == $t ){
										
									// Find the latest post for the given subforum 
									$latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostBySubforum($subforum);
										
									// Find the latest thread for the given subforum 
									$latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadBySubforum($subforum);
										
									// The methods findLatestThreadBySubforum and findLatestPostBySubforum returns an array
									// We have to loop through the array even if it just a single element in it 
										
									foreach($latestPost as $lp){
										$post = $lp;
									}
									foreach($latestThread as $lt){
										$thread = $lt;
									}
									
									// Check if both are null, this means there are no threads and posts 
									if ( !($post == null) || !($thread == null)) {
										// Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
										if ((!($thread == null )) && (!($post == null))) {
											if ($post->getDateTime() >= $thread->getDateTime() ){        
												// Add post to the array 
												$latestActivity[$forum->getId()] = $post;
											}
										}
										// Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
										if ((!($thread == null )) && (!($post == null))) {
											if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null )) && (!($post == null))  ){
												// Add thread to the array 
												$latestActivity[$forum->getId()] = $thread;
											}
										}
										// If the latestPost array is empty, we add the thread
										elseif ( ($post == null ) && (!($thread == null)) ) {
											// Add thread to the array 
											$latestActivity[$forum->getId()] = $thread;
										}
										// If the latestThread array is empty, we add the post
										elseif ( ($thread == null ) && (!($post == null )) ){
											// Add post to the array 
											$latestActivity[$forum->getId()] = $post;
										}
									}
								}		
							}
						}
					}
				}
			}
			
			
		}
		elseif ($this->get('security.context')->isGranted('ROLE_USER')){
		
			// Find the current user 
			$user= $this->get('security.context')->getToken()->getUser();
			
			// Find all the active assistant histories of the user 
			$activeAssistantHistories = $this->getDoctrine()->getRepository('AppBundle:AssistantHistory')->findActiveAssistantHistoriesByUser($user);
			
			// Find all forums
			$allForums = $this->getDoctrine()->getRepository('AppBundle:Forum')->findAll();
			
			// empty array, should contain the forums the user is allowed to access 
			$forums = array();
			
			foreach ($allForums as $forum){
				// If it is a general forum we add it to the array
				if ( $forum->getType() == "general" ){
					$forums[] = $forum;
				}
			}
			
			// Check each active assistant histories 
			foreach ($activeAssistantHistories as $ash){
				// Loop through each forum
				foreach ($allForums as $forum){
					
					// Find all the subforums of the forum
					$subforums = $forum->getSubforums();
						
					// Loop through all the subforums 
					foreach($subforums as $sf){
							
						// Find the schools associated with the subforum
						$schools = $sf->getSchools();
							
						// Loop through each school
						foreach ($schools as $s){
								
							// Check if the user is actively working at that school
							if ($ash->getSchool() == $s){
							
								// Check if the forum is already added
								if(!(in_array($forum, $forums))){
									$forums[] = $forum;
								}
									
							}		
						}
					}
				}
			}		
			
			
			// Loop through each forum the user is allowed to visit 
			foreach($forums as $forum) {
				
				// Initate variables 
				$post = null;
				$thread = null;
				
				// If the forum is general we can just find the latest posts and threads as we did with ROLE_ADMIN
				if ( $forum->getType() == "school" ){
						
					// Find the latest post for the given forum 
					$latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostByForum($forum);
							
					// Find the latest thread for the given forum 
					$latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadByForum($forum);
								
					// The methods findLatestThreadByForum and findLatestPostByForum returns an array
					// We have to loop through the array even if it just a single element in it 	
					foreach($latestPost as $lp){
						$post = $lp;
					}
					foreach($latestThread as $lt){
						$thread = $lt;
					}
					
					// Check if both are null, this means there are no threads and posts 
					if ( !($post == null) || !($thread == null)) {
						// Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
						if ((!($thread == null )) && (!($post == null))) {
							if ($post->getDateTime() >= $thread->getDateTime() ){        
								// Add post to the array 
								$latestActivity[$forum->getId()] = $post;
							}
						}
						// Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
						if ((!($thread == null )) && (!($post == null))) {
							if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null )) && (!($post == null))  ){
								// Add thread to the array 
								$latestActivity[$forum->getId()] = $thread;
							}
						}
						// If the latestPost array is empty, we add the thread
						elseif ( ($post == null ) && (!($thread == null)) ) {
							// Add thread to the array 
							$latestActivity[$forum->getId()] = $thread;
						}
						// If the latestThread array is empty, we add the post
						elseif ( ($thread == null ) && (!($post == null )) ){
							// Add post to the array 
							$latestActivity[$forum->getId()] = $post;
						}
					}
				}
				// If the forum is not general we have to check whether the user is associated with the forum through activeAssistantHistories 
				else {
					foreach ($activeAssistantHistories as $ash){
						// Find all the subforums of the forum
						$subforums = $forum->getSubforums();
									
						// Loop through all the subforums 
						foreach($subforums as $subforum){
							
							// Find the schools associated with the subforum
							$schools = $subforum->getSchools();
								
							// Loop through each school
							foreach ($schools as $s){

								// Check if the user is actively working at that school
								if ($ash->getSchool() == $s ){
										
									// Find the latest post for the given subforum 
									$latestPost = $this->getDoctrine()->getRepository('AppBundle:Post')->findLatestPostBySubforum($subforum);
										
									// Find the latest thread for the given subforum 
									$latestThread = $this->getDoctrine()->getRepository('AppBundle:Thread')->findLatestThreadBySubforum($subforum);
										
									// The methods findLatestThreadBySubforum and findLatestPostBySubforum returns an array
									// We have to loop through the array even if it just a single element in it 
										
									foreach($latestPost as $lp){
										$post = $lp;
									}
									foreach($latestThread as $lt){
										$thread = $lt;
									}
									
									// Check if both are null, this means there are no threads and posts 
									if ( !($post == null) || !($thread == null)) {
										// Check whether post has a higher or equal value to the thread datetime, and both the latestPost and latestThread is not empty
										if ((!($thread == null )) && (!($post == null))) {
											if ($post->getDateTime() >= $thread->getDateTime() ){        
												// Add post to the array 
												$latestActivity[$forum->getId()] = $post;
											}
										}
										// Check whether thread has a higher or equal value to the post datetime, and both the latestPost and latestThread is not empty
										if ((!($thread == null )) && (!($post == null))) {
											if ($post->getDateTime() <= $thread->getDateTime() && (!($thread == null )) && (!($post == null))  ){
												// Add thread to the array 
												$latestActivity[$forum->getId()] = $thread;
											}
										}
										// If the latestPost array is empty, we add the thread
										elseif ( ($post == null ) && (!($thread == null)) ) {
											// Add thread to the array 
											$latestActivity[$forum->getId()] = $thread;
										}
										// If the latestThread array is empty, we add the post
										elseif ( ($thread == null ) && (!($post == null )) ){
											// Add post to the array 
											$latestActivity[$forum->getId()] = $post;
										}
									}
								}		
							}
						}
					}
				}
			}
		}
		else {
			return $this->redirect($this->generateUrl('home'));
		}
		
		// Return the view to be rendered
		return $this->render('forum/index.html.twig', array(
			'forums' => $forums,
			'latestActivity' => $latestActivity,
		));
			
	}

}
