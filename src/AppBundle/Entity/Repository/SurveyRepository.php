<?php
/**
 * Created by IntelliJ IDEA.
 * User: Amir Ahmed
 * Date: 20.09.2018
 * Time: 19:39
 */

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Survey;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class SurveyRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Survey[]
     */
    public function findOneByUserNotTaken(User $user)
    {
        $qb = $this->_em->createQueryBuilder();

        $exclude = $qb
                ->select('IDENTITY(survey_taken.survey)')
                ->from('AppBundle:SurveyTaken', 'survey_taken')
                ->where('survey_taken.user = :user');

        return $this->createQueryBuilder('survey')
                ->select("survey")
                ->where('survey.teamSurvey = true')
                ->setParameter('user', $user)
                ->andWhere($qb->expr()->notIn('survey.id', $exclude->getDQL()))
                ->getQuery()
                ->getResult();
    }
}
