<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityRepository;

class SemesterRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryForAllSemestersOrderedByAge()
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->orderBy('Semester.semesterStartDate', 'DESC');
    }

    public function findAllOrderedByAge()
    {
        return $this->queryForAllSemestersOrderedByAge()->getQuery()->getResult();
    }

    /**
     * @return Semester
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrentSemester()
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->andWhere('Semester.semesterStartDate < :now')
            ->andWhere('Semester.semesterEndDate > :now')
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $semesterTime
     * @param string $year
     *
     * @return Semester[]
     */
    public function findByTimeAndYear(string $semesterTime, string $year)
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.semesterTime = :semesterTime')
            ->andWhere('Semester.year = :year')
            ->setParameters(array(
                'semesterTime' => $semesterTime,
                'year' => $year,
            ))
            ->getQuery()
            ->getResult();
    }
}
