<?php

namespace App\Controller;

use App\Entity\Repository\ArticleRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Entity\Repository\SemesterRepository;
use App\Service\FileUploader;
use App\Service\LogService;
use App\Service\SlugMaker;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Article;
use App\Form\Type\ArticleType;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArticleAdminController is the controller responsible for the administrative article actions,
 * such as creating and deleting articles.
 */
class ArticleAdminController extends BaseController
{
    // Number of articles shown per page on the admin page
    const NUM_ARTICLES = 10;

    public function __construct(
        private ArticleRepository $articleRepo,
        private PaginatorInterface $paginator,
        private SlugMaker $slugMaker,
        private FileUploader $fileUploader,
        private LogService $logService,
        private EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Shows the main page of the article administration.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $articles = $this->articleRepo->findAllArticles();

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
     * @param Article $article
     *
     * @return Response
     */
    #[Route("/kontrollpanel/artikkel/kladd/{slug}", name: "article_show_draft")]
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

        if ($form->isSubmitted()) {
            $this->slugMaker->setSlugFor($article);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the author to the currently logged in user
            $article->setAuthor($this->getUser());

            $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
            $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
            if (!$imageSmall || !$imageLarge) {
                return new JsonResponse("Error", 400);
            }

            $article->setImageSmall($imageSmall);
            $article->setImageLarge($imageLarge);

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Artikkelen har blitt publisert.'
            );

            $this->logService->info("A new article \"{$article->getTitle()}\" by {$article->getAuthor()} has been published");

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
            $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
            if ($imageSmall) {
                $article->setImageSmall($imageSmall);
            }
            $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
            if ($imageLarge) {
                $article->setImageLarge($imageLarge);
            }

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash(
                'success',
                'Endringene har blitt publisert.'
            );

            $this->logService->info("The article \"{$article->getTitle()}\" was edited by {$this->getUser()}");

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

            $this->em->persist($article);
            $this->em->flush();

            $response['success'] = true;
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'code'    => $e->getCode(),
                'cause'   => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @param Article $article
     *
     * @return RedirectResponse
     */
    public function deleteAction(Article $article)
    {
        $this->em->remove($article);
        $this->em->flush();

        $this->addFlash("success", "Artikkelen ble slettet");

        return $this->redirectToRoute('articleadmin_show');
    }
}
