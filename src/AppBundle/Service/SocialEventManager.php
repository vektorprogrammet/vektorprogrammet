<?php
namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\SocialEvent;



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
     * @param Semester £$semester
     * @return // WHAT? []
     */
    public function getOrderedList(Department $department, Semester $semester)
    {
        //$repository = $this->em->getRepository('SocialEvent');
        $repository = $this->em->getRepository('SocialEvent');


        $allSocialEvents = $repository->findTodoListItemsBySemesterAndDepartment($semester, $department);

        $orderedList = array($allSocialEvents);

        return $orderedList;

    }

}