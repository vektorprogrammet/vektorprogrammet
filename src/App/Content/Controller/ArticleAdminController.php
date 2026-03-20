<?php

namespace App\Content\Controller;

use App\Content\Form\ArticleType;
use App\Content\Infrastructure\Entity\Article;
use App\Content\Infrastructure\Repository\ArticleRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use App\Support\Controller\BaseController;
use App\Support\Infrastructure\FileUploader;
use App\Support\Infrastructure\LogService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * ArticleAdminController is the controller responsible for the administrative article actions,
 * such as creating and deleting articles.
 */
class ArticleAdminController extends BaseController
{
    // Number of articles shown per page on the admin page
    public const NUM_ARTICLES = 10;

    public function __construct(
        private readonly ArticleRepository $articleRepo,
        private readonly PaginatorInterface $paginator,
        private readonly SluggerInterface $slugger,
        private readonly FileUploader $fileUploader,
        private readonly LogService $logService,
        private readonly EntityManagerInterface $em,
        DepartmentRepository $departmentRepo,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Shows the main page of the article administration.
     *
     * @return Response
     */
    #[Route('/kontrollpanel/artikkeladmin', name: 'articleadmin_show', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $articles = $this->articleRepo->findAllArticles();

        // Uses the knp_paginator bundle to separate the articles into pages.
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article_admin/index.html.twig', [
            'pagination' => $pagination,
            'articles' => $articles->getQuery()->getResult(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route('/kontrollpanel/artikkel/kladd/{slug}', name: 'article_show_draft')]
    public function showDraftAction(Article $article)
    {
        return $this->render('article/show.html.twig', ['article' => $article, 'isDraft' => true]);
    }

    /**
     * Shows and handles the submission of the article creation form.
     *
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/artikkeladmin/opprett', name: 'articleadmin_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $slug = $this->slugger->slug((string) $article->getTitle())->lower()->toString();
            $existingSlugs = $this->articleRepo->findSlugs();
            $base = $slug;
            $i = 2;
            while (in_array($slug, $existingSlugs, true)) {
                $slug = $base.'-'.$i;
                ++$i;
            }
            $article->setSlug($slug);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the author to the currently logged in user
            /** @var \App\Identity\Infrastructure\Entity\User|null $currentUser */
            $currentUser = $this->getUser();
            $article->setAuthor($currentUser);

            $imageSmall = $this->fileUploader->uploadArticleImage($request, 'imgsmall');
            $imageLarge = $this->fileUploader->uploadArticleImage($request, 'imglarge');
            if ($imageSmall === null || $imageLarge === null) {
                return new JsonResponse('Error', 400);
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

            return new JsonResponse('ok');
        } elseif ($form->isSubmitted()) {
            return new JsonResponse('Error', 400);
        }

        return $this->render('article_admin/form.html.twig', [
            'article' => $article,
            'title' => 'Legg til en ny artikkel',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Shows and handles the submission of the article edit form.
     * Uses the same form type as article creation.
     *
     * @return RedirectResponse|Response
     */
    #[Route('/kontrollpanel/artikkeladmin/rediger/{id}', name: 'articleadmin_edit', methods: ['GET', 'POST'])]
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

            return new JsonResponse('ok');
        } elseif ($form->isSubmitted()) {
            return new JsonResponse('Error', 400);
        }

        return $this->render('article_admin/form.html.twig', [
            'article' => $article,
            'title' => 'Endre artikkel',
            'form' => $form->createView(),
        ]);
    }

    /**
     * Set/unset the sticky boolean on the given article.
     * This method is intended to be called by an Ajax request.
     *
     * @return JsonResponse
     */
    #[Route('/kontrollpanel/artikkeladmin/sticky/{id}', name: 'articleadmin_sticky', methods: ['POST'])]
    public function stickyAction(Article $article)
    {
        try {
            if ($article->getSticky()) {
                $article->setSticky(false);
                $response = ['sticky' => false];
            } else {
                $article->setSticky(true);
                $response = ['sticky' => true];
            }

            $this->em->persist($article);
            $this->em->flush();

            $response['success'] = true;
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'code' => $e->getCode(),
                'cause' => 'Det oppstod en feil.',
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * @return RedirectResponse
     */
    #[Route('/kontrollpanel/artikkeladmin/slett/{id}', name: 'articleadmin_delete', methods: ['POST'])]
    public function deleteAction(Article $article)
    {
        $this->em->remove($article);
        $this->em->flush();

        $this->addFlash('success', 'Artikkelen ble slettet');

        return $this->redirectToRoute('articleadmin_show');
    }
}
