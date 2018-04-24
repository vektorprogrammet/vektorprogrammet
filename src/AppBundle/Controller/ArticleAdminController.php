<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use AppBundle\Form\Type\CropImageType;
use Symfony\Component\Routing\RouterInterface;

/**
 * ArticleAdminController is the controller responsible for the administrative article actions,
 * such as creating and deleting articles.
 */
class ArticleAdminController extends Controller
{
    // Number of articles shown per page on the admin page
    const NUM_ARTICLES = 10;

    /**
     * Shows the main page of the article administration.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAllArticles();

        // Uses the knp_paginator bundle to separate the articles into pages.
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article_admin/index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Shows and handles the submission of the article creation form.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $article       = new Article();
        $form          = $this->createForm(new ArticleType(), $article);
        $cropImageForm = $this->createForm(new CropImageType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Set the author to the currently logged in user
            $article->setAuthor($this->getUser());
            $draft = $request->request->get('draft');
            $article->setPublished(!$draft);

            $imageSmall = $this->get('app.file_uploader')->uploadArticleImage($request, 'imgsmall');
            $imageLarge = $this->get('app.file_uploader')->uploadArticleImage($request, 'imglarge');
            $article->setImageSmall($imageSmall);
            $article->setImageLarge($imageLarge);

            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Artikkelen har blitt publisert.'
            );

            $this->get('app.logger')->info("A new article \"{$article->getTitle()}\" by {$article->getAuthor()} has been published");

            return new JsonResponse("ok");
        } elseif ($form->isSubmitted()) {
            return new JsonResponse("Error", 400);
        }

        return $this->render('article_admin/form.html.twig', array(
            'article'       => $article,
            'title'         => 'Legg til en ny artikkel',
            'form'          => $form->createView(),
            'cropImageForm' => $cropImageForm->createView(),
        ));
    }

    /**
     * Shows and handles the submission of the article edit form.
     * Uses the same form type as article creation.
     *
     * @param Request $request
     * @param Article $article
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Article $article)
    {
        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em      = $this->getDoctrine()->getManager();
            $draft = $request->request->get('draft');
            $article->setPublished(!$draft);

            $imageSmall = $this->get('app.file_uploader')->uploadArticleImage($request, 'imgsmall');
            if ($imageSmall) {
                $article->setImageSmall($imageSmall);
            }
            $imageLarge = $this->get('app.file_uploader')->uploadArticleImage($request, 'imglarge');
            if ($imageLarge) {
                $article->setImageLarge($imageLarge);
            }
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Endringene har blitt publisert.'
            );

            $this->get('app.logger')->info("The article \"{$article->getTitle()}\" was edited by {$this->getUser()}");

            return new JsonResponse("ok");
        } elseif ($form->isSubmitted()) {
            return new JsonResponse("Error", 400);
        }

        return $this->render('article_admin/form.html.twig', array(
            'article' => $article,
            'title'   => 'Endre artikkel',
            'form'    => $form->createView(),
        ));
    }

    /**
     * Set/unset the sticky boolean on the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @param Article $article
     *
     * @return JsonResponse
     */
    public function stickyAction(Article $article)
    {
        try {
            if ($article->getSticky()) {
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
            $response = [
                'success' => false,
                'code'    => $e->getCode(),
                'cause'   => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Deletes the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @param Article $article
     *
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
            $response = [
                'success' => false,
                'code'    => $e->getCode(),
                'cause'   => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }
}
