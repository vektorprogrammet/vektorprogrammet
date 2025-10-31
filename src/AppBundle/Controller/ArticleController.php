<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Service\Contract\ArticleServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ArticleController is the controller responsible for articles,
 * such as showing and article and the showing the news page.
 */
class ArticleController extends BaseController
{
    // Number of articles shown on the news page.
    const NUM_ARTICLES = 10;

    // Number of articles shown in the news carousel on the home page.
    const NUM_CAROUSEL_ARTICLES = 5;

    // Number of articles shown at the bottom of the admission page.
    const NUM_ADMISSION_ARTICLES = 4;

    // Number of articles shown in the other news side bar.
    const NUM_OTHER_ARTICLES = 8;

    private $articleService;

    /**
     * @param ArticleServiceInterface $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Shows the news page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $pagination = $this->articleService->getPaginatedArticles(
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );
        $departments = $this->articleService->getAllDepartments();

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
        $pagination = $this->articleService->getPaginatedArticlesByDepartments(
            $department,
            $request->query->get('page', 1),
            self::NUM_ARTICLES
        );
        $departments = $this->articleService->getAllDepartments();

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
        $articles = $this->articleService->getLatestArticles(self::NUM_OTHER_ARTICLES, $excludeId);

        return $this->render('article/sidebar_other.html.twig', array('articles' => $articles));
    }

    /**
     * Shows the news carousel.
     *
     * @return Response
     */
    public function showCarouselAction()
    {
        $articles = $this->articleService->getCarouselArticles(self::NUM_CAROUSEL_ARTICLES);

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
        $articles = $this->articleService->getDepartmentArticles($id, self::NUM_ADMISSION_ARTICLES);

        return $this->render('article/department_news.html.twig', array('articles' => $articles));
    }
}
