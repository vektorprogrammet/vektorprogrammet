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
     * @return Survey
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByUserNotTaken(User $user) : ?Survey
        {
            return $this->createQueryBuilder('survey')
                ->select("survey")
                //->join('survey.surveysTaken','surveysTaken')
                ->where('survey.teamSurvey = true')
                //->where(':user NOT IN surveysTaken')
                //->setParameter('user', $user)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

        }
}