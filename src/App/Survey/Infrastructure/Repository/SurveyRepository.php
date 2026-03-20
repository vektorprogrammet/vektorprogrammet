<?php

namespace App\Survey\Infrastructure\Repository;

use App\Shared\Entity\Semester;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyTaken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SurveyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Survey::class);
    }

    /**
     * @return Survey[]
     */
    public function findAllNotTakenByUserAndSemester(User $user, Semester $semester)
    {
        $department = $user->getDepartment();
        $qb = $this->_em->createQueryBuilder();
        $exclude = $qb
                ->select('IDENTITY(survey_taken.survey)')
                ->from(SurveyTaken::class, 'survey_taken')
                ->where('survey_taken.user = :user');

        return $this->createQueryBuilder('survey')
                ->select('survey')
                ->where('survey.targetAudience = :teamSurvey')
                ->andWhere('survey.semester =:semester')
                ->andWhere('survey.department =:department OR survey.department is NULL')
                ->andWhere($qb->expr()->notIn('survey.id', $exclude->getDQL()))
                ->setParameter('user', $user)
                ->setParameter('semester', $semester)
                ->setParameter('department', $department)
                ->setParameter('teamSurvey', Survey::$TEAM_SURVEY)
                ->getQuery()
                ->getResult();
    }
}
