<?php

namespace AppBundle\Entity\Repository;

use \Doctrine\ORM\EntityRepository;
use \AppBundle\Entity\Semester;

/**
 * Class ToDoItemRepository
 */
class ToDoItemRepository extends EntityRepository
{

    /**
     * @param Semester $semester
     * @return array
     */
    public function findToDoListItemsBySemester(Semester $semester)
    {
        return $this->createQueryBuilder('toDoListItem')
            ->select('toDoListItem')
            ->where('toDoListItem.semester = :semester')
            ->orWhere('toDoListItem.semester IS NULL')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Semester $semester
     * @return array
     */
    public function findCommonToDoListItems(Semester $semester)
    {
        return $this->createQueryBuilder('toDoListItem')
            ->select('toDoListItem')
            ->where('toDoListItem.semester = :semester')
            ->orWhere('toDoListItem.semester IS NULL')
            ->andWhere('toDoListItem.department IS NULL')
            ->andWhere('toDoListItem.deletedAt IS NOT NULL')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }


    /**
     * @return array
     */
    public function findMandatoryToDoListItems()
    {
        return $this->createQueryBuilder('toDoListItem')
                ->select('toDoMandatory')
                ->join("toDoItem", 'toDoItem', Expr\Join::WITH, "toDoMandatory.getToDoItem = toDoItem")
                ->getQuery()
                ->getResult();
    }

    /**
     * @param Semester $semester
     * @return array
     */
    public function findCompletedToDoListItems(Semester $semester)
    {
        return $this->createQueryBuilder('toDoListItem')
            ->join('toDoListItem.toDoCompleted', 'completed')
            ->where('completed.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }



    /**
     * @param Semester $semester
     * @return array
     */
    public function findToDoListItemsWithDeadLines(Semester $semester)
    {
        return $this->createQueryBuilder('toDoListItem')
            ->join('toDoListItem.toDoDeadlines', 'deadlines')
            ->where('deadlines.semester = :semester')
            ->setParameter('semester', $semester)
            ->getQuery()
            ->getResult();
    }
}
