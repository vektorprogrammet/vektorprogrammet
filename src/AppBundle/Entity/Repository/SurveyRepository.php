<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Semester;
use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class SurveyRepository extends EntityRepository
{

    /**
     * @param User $user
     * @param Semester $semester
     * @return Survey[]
     */
    public function findAllNotTakenByUserAndSemester(User $user, Semester $semester)
    {
        $department = $user->getDepartment();
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
                ->select('IDENTITY(survey_taken.survey)')
                ->from('AppBundle:SurveyTaken', 'survey_taken')
                ->where('survey_taken.user = :user');

        return $this->createQueryBuilder('survey')
                ->select("survey")
                ->where('survey.targetAudience = :teamSurvey')
                ->andWhere('survey.semester =:semester')
                ->andWhere('survey.department =:department OR survey.department is NULL')
                ->andWhere($qb->expr()->notIn('survey.id', $exclude->getDQL()))
                ->setParameter('user', $user)
                ->setParameter('semester', $semester)
                ->setParameter('department', $department)
                ->setParameter('teamSurvey', Survey::$TEAM_SURVEY)
                ->getQuery()
                ->getResult();
    }
}
