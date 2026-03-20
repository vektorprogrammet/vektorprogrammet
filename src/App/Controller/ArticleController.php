<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Department;
use App\Entity\Repository\ArticleRepository;
use App\Entity\Repository\DepartmentRepository;
use App\Shared\Repository\SemesterRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * ArticleController is the controller responsible for articles,
 * such as showing and article and the showing the news page.
 */
class ArticleController extends BaseController
{
    // Number of articles shown on the news page.
    public const NUM_ARTICLES = 10;

    // Number of articles shown in the news carousel on the home page.
    public const NUM_CAROUSEL_ARTICLES = 5;

    // Number of articles shown at the bottom of the admission page.
    public const NUM_ADMISSION_ARTICLES = 4;

    // Number of articles shown in the other news side bar.
    public const NUM_OTHER_ARTICLES = 8;

    public function __construct(
        private readonly ArticleRepository $articleRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly PaginatorInterface $paginator,
        SemesterRepository $semesterRepo,
    ) {
        parent::__construct($departmentRepo, $semesterRepo);
    }

    /**
     * Shows the news page.
     *
     * @return Response
     */
    #[Route('/nyheter', name: 'news', methods: ['GET'])]
    public function showAction(Request $request)
    {
        $articles = $this->articleRepo->findAllPublishedArticles();

        $departments = $this->departmentRepo->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
        ]);
    }

    /**
     * Shows the news page, with articles for all departments and the given department.
     *
     * @return Response
     */
    #[Route('/nyheter/{department}', name: 'article_filter', methods: ['GET'])]
    public function showFilterAction(Request $request, $department)
    {
        $articles = $this->articleRepo->findAllArticlesByDepartments($department);

        $departments = $this->departmentRepo->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
            'departments' => $departments,
        ]);
    }

    /**
     * Shows the given article.
     *
     * @return Response
     */
    #[Route('/nyhet/{slug}', name: 'article_show', methods: ['GET'])]
    #[Route('/artikkel/{slug}', name: 'old_article_show', methods: ['GET'])]
    public function showSpecificAction(Article $article)
    {
        if (!$article->isPublished()) {
            throw $this->createNotFoundException();
        }

        return $this->render('article/show.html.twig', ['article' => $article]);
    }

    /**
     * Shows a list of the latest articles excluding the article with the given id.
     *
     * @return Response
     */
    public function showOtherAction($excludeId)
    {
        $articles = $this->articleRepo
            ->findLatestArticles(self::NUM_OTHER_ARTICLES, $excludeId);

        return $this->render('article/sidebar_other.html.twig', ['articles' => $articles]);
    }

    /**
     * Shows the news carousel.
     *
     * @return Response
     */
    public function showCarouselAction()
    {
        $articles = $this->articleRepo->findStickyAndLatestArticles(self::NUM_CAROUSEL_ARTICLES);

        return $this->render('article/carousel.html.twig', ['articles' => $articles]);
    }

    /**
     * Shows a set of news for the given department.
     * Is used to show the news on each of the admission pages.
     *
     * @return Response
     */
    public function showDepartmentNewsAction($id)
    {
        $articles = $this->articleRepo->findLatestArticlesByDepartment($id, self::NUM_ADMISSION_ARTICLES);

        return $this->render('article/department_news.html.twig', ['articles' => $articles]);
    }
}
