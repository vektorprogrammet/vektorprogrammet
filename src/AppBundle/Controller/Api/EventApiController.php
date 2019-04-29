<?php

namespace AppBundle\Controller\Api;

use AppBundle\DataTransferObject\UserDto;
use AppBundle\Entity\User;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class EventApiController extends BaseController
// test
{

    /**
     * @Route("api/user/events")
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \BCC\AutoMapperBundle\Mapper\Exception\InvalidClassConstructorException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserAction()
    {
        if (!$this->getUser()) {
            return new JsonResponse(null);
        }
        # TODO : ADD TO THIS.
        return new JsonResponse(null);

    }

}