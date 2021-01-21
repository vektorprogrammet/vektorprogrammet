<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ParentCourseRepository extends EntityRepository
{
    public function findAllParentCoursesOrderedByDate()
    {
        return $this->createQueryBuilder('parent_course')
            ->select('parent_course')
            ->orderBy("parent_course.date", "ASC")
            ->getQuery()
            ->getResult();
    }
}
