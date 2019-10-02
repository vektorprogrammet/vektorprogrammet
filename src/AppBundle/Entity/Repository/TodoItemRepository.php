<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Department;
use AppBundle\Entity\TodoItem;
use \Doctrine\ORM\EntityRepository;
use \AppBundle\Entity\Semester;

/**
 * Class TodoItemRepository
 */
class TodoItemRepository extends EntityRepository
{

    /**
     * @param Semester $semester
     * @param Department $department
     * @return array
     */
    public function findTodoListItemsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        $todoItems = $this->createQueryBuilder('todoListItem')
            ->select('todoListItem')
            ->where('todoListItem.semester = :semester or todoListItem.semester is null')
            ->andWhere('todoListItem.department = :department or todoListItem.department is null')
            ->andWhere('todoListItem.deletedAt > :semesterEndDate or todoListItem.deletedAt is null')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->setParameter('semesterEndDate', $semester->getEndDate())
            ->getQuery()
            ->getResult();

        $filteredItems = array_filter($todoItems, function (TodoItem $item) use ($semester) {
            if (empty($item->getSemester())) {
                return (
                    $item->getCreatedAt() < $semester->getEndDate() &&
                    (empty($item->getDeletedAt())? true : $item->getDeletedAt() > $semester->getStartDate()));
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
    public function findCompletedTodoListItemsBySemesterAndDepartment(Semester $semester, Department $department)
    {
        return $this->createQueryBuilder('todoListItem')
            ->join('todoListItem.todoCompleted', 'completed')
            ->where('completed.semester = :semester')
            ->andWhere('completed.department = :department')
            ->andWhere('todoListItem.deletedAt > :semesterEndDate or todoListItem.deletedAt is null')
            ->setParameter('semester', $semester)
            ->setParameter('department', $department)
            ->setParameter('semesterEndDate', $semester->getEndDate())
            ->getQuery()
            ->getResult();
    }
}
