<?php

namespace AppBundle\Controller;

use AppBundle\Service\Contract\HomeServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    private $homeService;

    /**
     * @param HomeServiceInterface $homeService
     */
    public function __construct(HomeServiceInterface $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * @return Response
     */
    public function showAction()
    {
        $data = $this->homeService->getHomePageData();

        return $this->render('home/index.html.twig', $data);
    }

    public function postAction()
    {
        return $this->redirect("https://www.youtube.com/watch?v=dQw4w9WgXcQ?autoplay=1", 301);
    }
}
