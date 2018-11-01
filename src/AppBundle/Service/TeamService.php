<?php
/**
 * Created by IntelliJ IDEA.
 * User: amir
 * Date: 29.10.18
 * Time: 18:36
 */

namespace AppBundle\Service;


use AppBundle\Entity\Semester;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class TeamService
{

    private $em;

    /**
     * TeamService constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function getTeamsActiveInBySemesterByUser(User $user, Semester $semester) : array
    {
        $teams = $this->em->getRepository('AppBundle:Team')->findByUserAndSemester($user, $semester);
        $teamNames = array();
        foreach ($teams as $team) {
            $teamName = $team->getName();

            if(in_array($teamName, $teamNames)){
                continue;
            }
            $teamNames[] = $teamName;
        }

        if(empty($teamNames)) {
            $teamNames[] = "Ikke teammedlem";
        }

        return $teamNames;
    }

}