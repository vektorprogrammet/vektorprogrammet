<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Survey;
use AppBundle\Entity\SurveyTaken;
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
}
