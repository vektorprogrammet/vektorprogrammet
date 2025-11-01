<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyTaken;
use AppBundle\Entity\User;
use AppBundle\Repository\Contract\SurveyTakenRepositoryInterface;
use Doctrine\ORM\EntityRepository;

class SurveyTakenRepository extends EntityRepository implements SurveyTakenRepositoryInterface
{
    /**
     * @param Survey $survey
     *
     * @return SurveyTaken[]
     */
    public function findAllTakenBySurvey(Survey $survey): array
    {
        $qb = $this->createQueryBuilder('st')
            ->select('st')
            ->where('st.survey = :survey')
            ->setParameter('survey', $survey);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Survey $survey
     * @param User $user
     *
     * @return SurveyTaken[]
     */
    public function findAllBySurveyAndUser(Survey $survey, User $user): array
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
