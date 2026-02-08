<?php

namespace App\Entity\Repository;

use App\Entity\Survey;
use App\Entity\SurveyTaken;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class SurveyTakenRepository extends EntityRepository
{
    /**
     * @param Survey $survey
     *
     * @return SurveyTaken[]
     */
    public function findAllTakenBySurvey(Survey $survey):array
    {
        $qb = $this->createQueryBuilder('st')
            ->select('st')
            ->where('st.survey = :survey')
            ->setParameter('survey', $survey);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param Survey $survey
     *
     * @return SurveyTaken[]
     *
     *
     */
    public function findAllBySurveyAndUser(Survey $survey, User $user):array
    {
        $qb = $this->createQueryBuilder('st')
            ->select('st')
            ->where('st.survey = :survey')
            ->andWhere('st.user = :user')
            ->setParameter('survey', $survey)
            ->setParameter('user', $user);


        return $qb->getQuery()->getResult();
    }
}
