<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Article;

/**
 * ArticleController is the controller responsible for articles,
 * such as showing and article and the showing the news page.
 */
class ArticleController extends Controller
{
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAllPublishedArticles();

        $departments = $em->getRepository('AppBundle:Department')->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFilterAction(Request $request, $department)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findAllArticlesByDepartments($department);

        $departments = $em->getRepository('AppBundle:Department')->findAllDepartments();

        // Uses the knp_paginator bundle to separate the articles into pages
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
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
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOtherAction($excludeId)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')
            ->findLatestArticles(self::NUM_OTHER_ARTICLES, $excludeId);

        return $this->render('article/sidebar_other.html.twig', array('articles' => $articles));
    }

    /**
     * Shows the news carousel.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCarouselAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findStickyAndLatestArticles(self::NUM_CAROUSEL_ARTICLES);

        return $this->render('article/carousel.html.twig', array('articles' => $articles));
    }

    /**
     * Shows a set of news for the given department.
     * Is used to show the news on each of the admission pages.
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showDepartmentNewsAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em->getRepository('AppBundle:Article')->findLatestArticlesByDepartment($id, self::NUM_ADMISSION_ARTICLES);

        return $this->render('article/department_news.html.twig', array('articles' => $articles));
    }
}
