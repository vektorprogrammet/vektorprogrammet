<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use AppBundle\Form\Type\CropImageType;

/**
 * ArticleAdminController is the controller responsible for the administrative article actions,
 * such as creating and deleting articles.
 *
 * @package AppBundle\Controller
 */
class ArticleAdminController extends Controller
{
    // Number of articles shown per page on the admin page
    const NUM_ARTICLES = 10;

    /**
     * Shows the main page of the article administration.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAllArticles();

        // Uses the knp_paginator bundle to separate the articles into pages.
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            ArticleAdminController::NUM_ARTICLES
        );

        return $this->render('article_admin/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Shows and handles the submission of the article creation form.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article);
        $cropImageForm = $this->createForm(new cropImageType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Set the author to the currently logged in user
            $article->setAuthor($this->getUser());

            // Don't persist if the preview button was clicked
            if (false === $form->get('preview')->isClicked()) {
                $em->persist($article);
                $em->flush();

                $this->addFlash(
                    'article-notice',
                    'Artikkelen har blitt publisert.'
                );

                return $this->redirect($this->generateUrl('article_show', array('id' => $article->getId())));
            }

            // Render the article as a preview
            return $this->render('article/show.html.twig', array('article' => $article));
        }

        return $this->render('article_admin/form.html.twig', array(
            'title' => 'Legg til en ny artikkel',
            'form' => $form->createView(),
            'cropImageForm' => $cropImageForm->createView()
        ));
    }

    /**
     * Shows and handles the submission of the article edit form.
     * Uses the same form type as article creation.
     *
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Article $article)
    {
        $form = $this->createForm(new ArticleType(), $article);
        $cropImageForm = $this->createForm(new cropImageType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Don't persist if the preview button was clicked
            if (false === $form->get('preview')->isClicked()) {
                $em->persist($article);
                $em->flush();

                $this->addFlash(
                    'article-notice',
                    'Endringene har blitt publisert.'
                );

                return $this->redirect($this->generateUrl('article_show', array('id' => $article->getId())));
            }

            // Render the article as a preview
            return $this->render('article/show.html.twig', array('article' => $article));
        }

        return $this->render('article_admin/form.html.twig', array(
            'title' => 'Endre artikkel',
            'form' => $form->createView(),
            'cropImageForm' => $cropImageForm->createView()
        ));
    }

    /**
     * Set/unset the sticky boolean on the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function stickyAction(Article $article)
    {
        try {
            if($article->getSticky()) {
                $article->setSticky(false);
                $response['sticky'] = false;
            } else {
                $article->setSticky(true);
                $response['sticky'] = true;
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $response['success'] = true;
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.'
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Deletes the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function deleteAction(Article $article)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            $response['success'] = true;
        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.'
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * NOT IN USE
     *
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cropImageAction(Request $request) {
        $form = $this->createForm(new cropImageType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $image = $data['image'];
            $largeCropData = json_decode($data['largeCropData'], true);
            $mediumCropData = json_decode($data['mediumCropData'], true);
            $smallCropData = json_decode($data['smallCropData'], true);

            # The article image folder
            $articleImageFolder = $this->container->getParameter('article_images');

            # Crop image to the three sizes used by the website and return their urls
            try {
                $imageLarge = $this->crop($image, $largeCropData, $request, $articleImageFolder.'/large/');
                $imageMedium = $this->crop($image, $mediumCropData, $request, $articleImageFolder.'/medium/');
                $imageSmall = $this->crop($image, $smallCropData, $request, $articleImageFolder.'/small/');

                $response = [
                    'success' => true,
                    'imageLarge' => $imageLarge,
                    'imageMedium' => $imageMedium,
                    'imageSmall' => $imageSmall
                ];

            } catch (\Exception $e) {
                $response = ['success' => false,
                    'code' => $e->getCode(),
                    'cause' => 'Det oppstod en feil under behandlingen av bildet. Prøv igjen eller kontakt IT ansvarlig.'
                ];
            }

            return new JsonResponse($response);
        }

        return new JsonResponse([
            'success' => false,
            'cause' => 'Utfylt informasjon er ugyldig.'
        ]);
    }

    /**
     * NOT IN USE
     *
     * Crops an image using LiipImagineBundle. Moves the image from cache to the specified location.
     *
     * @param $image
     * @param $cropData
     * @param $request
     * @param $location
     * @return string
     */
    public function crop($image, $cropData, $request, $location)
    {
        # Check if the location folder exists, if not create it
        if (!file_exists($location)) {
            mkdir($location, 0777, true);
        }

        # The filter
        $filter = 'article_crop';

        # The cache folder
        $cache = 'media/cache/';

        $container = $this->container;

        # The controller service
        $imagemanagerResponse = $container->get('liip_imagine.controller');

        # The filter configuration service
        $filterConfig = $container->get('liip_imagine.filter.configuration');

        # Get the filter settings
        $config = $filterConfig->get($filter);

        # Update filter settings
        $config['filters']['relative_resize']['scale'] = $cropData['scale'];
        $config['filters']['crop']['size'] = array($cropData['w'], $cropData['h']);
        $config['filters']['crop']['start'] = array($cropData['x'], $cropData['y']);
        $filterConfig->set($filter, $config);

        # Apply the filter
        $imagemanagerResponse->filterAction($request, $image, $filter);

        # Move the img from temp
        rename($cache.$filter.$image, $location.basename($image));

        return $location.basename($image);
    }


    /**
     * This method is intended to be called by an Ajax request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveImageAction(Request $request) {

        //Get all information from the request
        $content = $request->getContent();
        $data = json_decode($content,true);
        $aviaryURL = $data['aviaryURL'];
        $imageType = $data['imageType'];
        $cropped = $data['cropped'];
        $file_name = $data['filename'];

        //Remove previous postfixs from the name
        $file_name = str_replace(array("_cropped","_edited","_small","_medium","_large"),"",$file_name);

        //Get desired location, and find correct subfolder depending on image type.
        $articleImageFolder = $this->container->getParameter('article_images');
        $sub_folder = ($cropped) ? strtolower($imageType) : 'edited';
        $location = $articleImageFolder.'/'.$sub_folder.'/';

        //Create the directory if it does not exist
        if (!file_exists($location)) {
            mkdir($location, 0777, true);
        }

        //Append new postfixs
        $local_file_path = $location . $file_name;
        if(!$cropped){
            $local_file_path .= '_edited';
            $local_file_path .= ($imageType === 'All') ? '' : '_' . strtolower($imageType);
        }
        else{
            $local_file_path .= '_cropped';
        }
        $local_file_path .= '.jpg';

        try {
            //copy the image to the new location
            copy($aviaryURL, $local_file_path);

            $response = [
                'success' => true,
                'localURL' => $this->get('request')->getBasePath() . '/' . $local_file_path,
                'imageType' => $imageType,
            ];

        } catch (\Exception $e) {
            $response = ['success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil under lagringen av bildet. Prøv igjen eller kontakt IT ansvarlig.'
            ];
        }

        return new JsonResponse($response);

    }
}