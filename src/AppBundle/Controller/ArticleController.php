<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Department;
use AppBundle\Repository\Contract\ArticleRepositoryInterface;
use AppBundle\Repository\Contract\DepartmentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArticleController is the controller responsible for articles,
 * such as showing and article and the showing the news page.
 */
class ArticleController extends BaseController
{
    private $articleRepository;
    private $departmentRepository;
    private $paginator;
    private $entityManager;

    /**
     * @param ArticleRepositoryInterface $articleRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ArticleRepositoryInterface $articleRepository,
        DepartmentRepositoryInterface $departmentRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $entityManager
    ) {
        $this->articleRepository = $articleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->paginator = $paginator;
        $this->entityManager = $entityManager;
    }
    // Number of articles shown on the news page.
    const NUM_ARTICLES = 10;

    // Number of articles shown in the news carousel on the home page.
    const NUM_CAROUSEL_ARTICLES = 5;

    // Number of articles shown at the bottom of the admission page.
    const NUM_ADMISSION_ARTICLES = 4;

    // Number of articles shown in the other news side bar.
    const NUM_OTHER_ARTICLES = 8;

    /**
     * Shows the news page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $articles = $this->articleRepository->findAllPublishedArticles();

        $departments = $this->departmentRepository->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', array(
            'pagination' => $pagination,
            'departments' => $departments,
        ));
    }

    /**
     * Shows the news page, with articles for all departments and the given department.
     *
     * @param Request $request
     * @param $department
     *
     * @return Response
     */
    public function showFilterAction(Request $request, $department)
    {
        $articles = $this->articleRepository->findAllArticlesByDepartments($department);

        $departments = $this->departmentRepository->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $pagination = $this->paginator->paginate(
            $articles,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );

        return $this->render('article/index.html.twig', array(
            'pagination' => $pagination,
            'departments' => $departments,
        ));
    }

    /**
     * Shows the given article.
     *
     * @param Article $article
     *
     * @return Response
     */
    public function showSpecificAction(Article $article)
    {
        if (!$article->isPublished()) {
            throw $this->createNotFoundException();
        }
        return $this->render('article/show.html.twig', array('article' => $article));
    }

    /**
     * Shows a list of the latest articles excluding the article with the given id.
     *
     * @param $excludeId
     *
     * @return Response
     */
    public function showOtherAction($excludeId)
    {
        $articles = $this->articleRepository
            ->findLatestArticles(self::NUM_OTHER_ARTICLES, $excludeId);

        return $this->render('article/sidebar_other.html.twig', array('articles' => $articles));
    }

    /**
     * Shows the news carousel.
     *
     * @return Response
     */
    public function showCarouselAction()
    {
        $articles = $this->articleRepository->findStickyAndLatestArticles(self::NUM_CAROUSEL_ARTICLES);

        return $this->render('article/carousel.html.twig', array('articles' => $articles));
    }

    /**
     * Shows a set of news for the given department.
     * Is used to show the news on each of the admission pages.
     *
     * @param $id
     *
     * @return Response
     */
    public function showDepartmentNewsAction($id)
    {
        $articles = $this->articleRepository->findLatestArticlesByDepartment($id, self::NUM_ADMISSION_ARTICLES);

        return $this->render('article/department_news.html.twig', array('articles' => $articles));
    }
}
