<?php

namespace AppBundle\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ReactIntroApiController extends AbstractFOSRestController
{
    /**
     *
     * @Route(
     *     "api/react_intro/quotes",
     *     methods={"GET"}
     * )
     *
     * @return JsonResponse
     */
    public function reactIntroTestApiAction()
    {
        $quotes = (array) [
            [
                'text' => "The greatest glory in living lies not in never falling, but in rising every time we fall.",
                'author' => "Nelson Mandela"
            ],
            [
                'text' => "The way to get started is to quit talking and begin doing.",
                'author' => "Walt Disney"
            ],
            [
                'text' => "Your time is limited, so don't waste it living someone else's life. Don't be trapped by dogma – which is living with the results of other people's thinking.",
                'author' => "Steve Jobs"
            ],
            [
                'text' => "If life were predictable it would cease to be life, and be without flavor.",
                'author' => "Eleanor Roosevelt"
            ],
            [
                'text' => "If you set your goals ridiculously high and it's a failure, you will fail above everyone else's success.",
                'author' => "James Cameron"
            ],

        ];
        return new JsonResponse($quotes);
    }
}
