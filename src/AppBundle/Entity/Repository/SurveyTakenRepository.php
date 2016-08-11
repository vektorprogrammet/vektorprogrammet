<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class SurveyTakenRepository extends EntityRepository
{
    public function findAllTakenBySurvey($survey)
    {
        $qb = $this->createQueryBuilder('st')
            ->select('st')
            ->where('st.survey = :survey')
            ->setParameter('survey', $survey);

        return $qb->getQuery()->getResult();
    }
}
