<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\ToDoItem;
use \Doctrine\ORM\EntityRepository;
use \AppBundle\Entity\Semester;

/**
 * Class ToDoItemRepository
 */
class ToDoItemRepository extends EntityRepository
{

    /**
     * @param Semester $semester
     * @param Department $department
     * @return array
     */
    public function findToDoListItemsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        $todoItems = $this->createQueryBuilder('toDoListItem')
            ->select('toDoListItem')
            ->where('toDoListItem.semester = :semester or toDoListItem.semester is null')
            ->andWhere('toDoListItem.department = :department or toDoListItem.department is null')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();

        $filteredItems = array_filter($todoItems, function (ToDoItem $item) use ($semester) {
            if (empty($item->getSemester())){
                return (
                    $item->getCreatedAt() < $semester->getSemesterEndDate() and
                    (empty($item->getDeletedAt())? True : $item->getDeletedAt() > $semester->getSemesterStartDate()));
            } else {
                return true;
            }
        });
        return $filteredItems;
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
}
