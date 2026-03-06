<?php

namespace App\Entity\Repository;

use App\Entity\Semester;
use App\Utils\SemesterUtil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SemesterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Semester::class);
    }

    /**
     * @return QueryBuilder
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
     * @throws NonUniqueResultException
     */
    public function findCurrentSemester()
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.year = :year')
            ->andWhere('Semester.semesterTime = :semesterTime')
            ->setParameters([
                'year' => SemesterUtil::timeToYear($now),
                'semesterTime' => SemesterUtil::timeToSemesterTime($now),
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Semester
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function findOrCreateCurrentSemester()
    {
        $semester = $this->findCurrentSemester();
        if ($semester == null) {
            // Create a new semester
            $now = new \DateTime();
            $semester = SemesterUtil::timeToSemester($now);
            $this->getEntityManager()->persist($semester);
            $this->getEntityManager()->flush();
        }

        return $semester;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByTimeAndYear(string $semesterTime, string $year): ?Semester
    {
        return $this->createQueryBuilder('Semester')
            ->select('Semester')
            ->where('Semester.semesterTime = :semesterTime')
            ->andWhere('Semester.year = :year')
            ->setParameters([
                'semesterTime' => $semesterTime,
                'year' => $year,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException|ORMException
     */
    public function getNextActive(Semester $semester): ?Semester
    {
        if ($semester === $this->findOrCreateCurrentSemester()) {
            return null;
        }
        if ($semester->getSemesterTime() === 'Høst') {
            return $this->findByTimeAndYear('Vår', (string) ((int) $semester->getYear() + 1));
        }

        return $this->findByTimeAndYear('Høst', $semester->getYear());
    }
}
