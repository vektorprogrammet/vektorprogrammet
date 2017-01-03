<?php
/**
 * Created by IntelliJ IDEA.
 * User: kristoffer
 * Date: 03.01.17
 * Time: 00:21.
 */

namespace AppBundle\Service;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;

class AssistantsData
{
    private $em;

    /**
     * AssistantsData constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCurrentAssistantDataByDepartment(Department $department, Semester $semester = null): array
    {
        $assistantHistories = $this->em->getRepository('AppBundle:AssistantHistory')->findAssistantHistoriesByDepartment($department, $semester);

        $groups = array(
            'Bolk 1' => array(),
            'Bolk 2' => array(),
        );
        foreach ($assistantHistories as $ah) {
            $day = $ah->getDay() !== '' ? $ah->getDay() : 'Uten dag';
            foreach (array('Bolk 1', 'Bolk 2') as $group) {
                if (strpos($ah->getBolk(), $group) !== false) {
                    if (!key_exists($ah->getSchool()->getName(), $groups[$group])) {
                        $groups[$group][$ah->getSchool()->getName()] = array();
                    }

                    if (!key_exists($day, $groups[$group][$ah->getSchool()->getName()])) {
                        $groups[$group][$ah->getSchool()->getName()][$day] = array();
                    }

                    array_push($groups[$group][$ah->getSchool()->getName()][$day], $ah->getUser());
                }
            }
        }

        return $groups;
    }
}
