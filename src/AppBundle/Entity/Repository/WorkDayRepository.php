<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\AssistantHistory;
use Doctrine\ORM\EntityRepository;

class WorkDayRepository extends EntityRepository
{
    public function findChronologicallyByAssistantPosition(AssistantHistory $assistantPosition)
    {
        return $this->createQueryBuilder('workDay')
            ->select('workDay')
            ->where('workDay.assistantPosition = :assistantPosition')
            ->setParameter('assistantPosition', $assistantPosition)
            ->orderBy('workDay.date')
            ->getQuery()
            ->getResult();
    }
}
