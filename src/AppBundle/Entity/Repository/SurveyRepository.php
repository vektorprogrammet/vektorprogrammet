<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class SurveyRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Department $department
     * @param Semester $semester
     * @return Survey[]
     */
    public function findOneByUserNotTaken(User $user, Semester $semester)
    {
        $department = $user->getDepartment();

        $qb = $this->_em->createQueryBuilder();

        $exclude = $qb
                ->select('IDENTITY(survey_taken.survey)')
                ->from('AppBundle:SurveyTaken', 'survey_taken')
                ->where('survey_taken.user = :user');

        return $this->createQueryBuilder('survey')
                ->select("survey")
                ->where('survey.teamSurvey = true')
                ->where('survey.semester =:semester')
                ->andWhere('survey.department =:department OR survey.department is NULL')
                ->andWhere($qb->expr()->notIn('survey.id', $exclude->getDQL()))
                ->setParameter('user', $user)
                ->setParameter('semester', $semester)
                ->setParameter('department', $department)
                ->getQuery()
                ->getResult();
    }
}
