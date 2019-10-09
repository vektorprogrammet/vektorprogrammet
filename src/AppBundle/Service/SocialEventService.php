<?php


namespace AppBundle\Service;

use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\This;

class SocialEventService
{
    private $socialEventRepository;
    private $currentSemester;
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->socialEventRepository = $em->getRepository('AppBundle:SocialEvent');
        $this->currentSemester = $em->getRepository('AppBundle:Semester')->findCurrentSemester();
    }

}
