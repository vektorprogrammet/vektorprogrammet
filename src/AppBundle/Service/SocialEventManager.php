<?php
namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\SocialEvent;
use Appbundle\Entity\Repository\SocialEventItemRepository;

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


    /**
     * @param Department $department
     * @param Semester $semester
     * @return array
     */
    public function getOrderedList(Department $department, Semester $semester)
    {
        $repository = $this->em->getRepository('AppBundle:SocialEvent');
        $allSocialEvents = $repository->findSocialEventsBySemesterAndDepartment($semester, $department);
        //$orderedList = array($allSocialEvents);
        //return $orderedList;

        return $allSocialEvents;
    }
}
