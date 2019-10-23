<?php
namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;

class SocialEventManager
{
    /**
     * @var EntityManager
     *
     */
    private $em;


    /**
     * TodoListService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
}
