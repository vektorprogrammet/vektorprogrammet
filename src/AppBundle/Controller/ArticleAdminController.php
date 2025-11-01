<?php

namespace AppBundle\Controller;

use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Service\Contract\ArticleManagementServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Article;
use AppBundle\Form\Type\ArticleType;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArticleAdminController is the controller responsible for the administrative article actions,
 * such as creating and deleting articles.
 */
class ArticleAdminController extends BaseController
{
    private $articleRepository;
    private $paginator;
    private $articleManagementService;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     * @param PaginatorInterface $paginator
     * @param ArticleManagementServiceInterface $articleManagementService
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        PaginatorInterface $paginator,
        ArticleManagementServiceInterface $articleManagementService
    ) {
        $this->articleRepository = $articleRepository;
        $this->paginator = $paginator;
        $this->articleManagementService = $articleManagementService;
    }

    // Number of articles shown per page on the admin page
    const NUM_ARTICLES = 10;

    /**
     * Shows the main page of the article administration.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $articles = $this->articleRepository->findAllArticles();

        // Uses the knp_paginator bundle to separate the articles into pages.
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article_admin/index.html.twig', array(
            'pagination' => $pagination,
            'articles' => $articles->getQuery()->getResult()
        ));
    }

    /**
     * @Route("/kontrollpanel/artikkel/kladd/{slug}", name="article_show_draft")
     * @param Article $article
     *
     * @return Response
     */
    public function showDraftAction(Article $article)
    {
        return $this->render('article/show.html.twig', array('article' => $article, 'isDraft' => true));
    }

    /**
     * Shows and handles the submission of the article creation form.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $article       = new Article();
        $form          = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->articleManagementService->createArticle($article, $this->getUser(), $request);

            if (!$result['success']) {
                return new JsonResponse("Error", 400);
            }

            $this->addFlash('success', 'Artikkelen har blitt publisert.');

            return new JsonResponse("ok");
        } elseif ($form->isSubmitted()) {
            return new JsonResponse("Error", 400);
        }

        return $this->render('article_admin/form.html.twig', array(
            'article'       => $article,
            'title'         => 'Legg til en ny artikkel',
            'form'          => $form->createView(),
        ));
    }

    /**
     * Shows and handles the submission of the article edit form.
     * Uses the same form type as article creation.
     *
     * @param Request $request
     * @param Article $article
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Article $article)
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->articleManagementService->updateArticle($article, $this->getUser(), $request);

            $this->addFlash('success', 'Endringene har blitt publisert.');

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
        $result = $this->articleManagementService->toggleStickyStatus($article);

        if (!$result['success']) {
            return new JsonResponse($result['error']);
        }

        return new JsonResponse([
            'success' => true,
            'sticky' => $result['sticky'],
        ]);
    }

    /**
     * @param Article $article
     *
     * @return RedirectResponse
     */
    public function deleteAction(Article $article)
    {
        $this->articleManagementService->deleteArticle($article);

        $this->addFlash("success", "Artikkelen ble slettet");

        return $this->redirectToRoute('articleadmin_show');
    }
}
