<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Semester;
use DateTime;
use Doctrine\ORM\EntityRepository;
use AppBundle\Utils\SemesterUtil;

class SemesterRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryForAllSemestersOrderedByAge()
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->addOrderBy('Semester.year', 'DESC')
            ->addOrderBy('Semester.semesterTime', 'ASC'); // Vår < Høst
    }

    public function findAllOrderedByAge()
    {
        return $this->queryForAllSemestersOrderedByAge()->getQuery()->getResult();
    }

    /**
     * @return Semester
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCurrentSemester()
    {
        $now = new DateTime();

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.year = :year')
            ->andWhere('Semester.semesterTime = :semesterTime')
            ->setParameters(array(
                'year' => SemesterUtil::timeToYear($now),
                'semesterTime' => SemesterUtil::timeToSemesterTime($now)
            ))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $semesterTime
     * @param string $year
     * @return Semester|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByTimeAndYear(string $semesterTime, string $year) : ? Semester
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
            ->getOneOrNullResult();
    }

    /**
     * @param Semester $semester
     * @return Semester|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNextActive(Semester $semester): ? Semester
    {
        if ($semester === $this->findCurrentSemester()) {
            return null;
        }
        if ($semester->getSemesterTime() === 'Høst') {
            return $this->findByTimeAndYear('Vår', (string)((int)($semester->getYear()) + 1));
        } else {
            return $this->findByTimeAndYear('Høst', $semester->getYear());
        }
    }
}
