<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use AppBundle\Repository\Contract\SemesterRepositoryInterface;
use AppBundle\Service\Contract\SemesterValidationServiceInterface;

/**
 * Service for semester validation logic.
 */
class SemesterValidationService implements SemesterValidationServiceInterface
{
    private $semesterRepository;

    /**
     * @param SemesterRepositoryInterface $semesterRepository
     */
    public function __construct(SemesterRepositoryInterface $semesterRepository)
    {
        $this->semesterRepository = $semesterRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findExistingSemester(int $semesterTime, int $year): ?Semester
    {
        return $this->semesterRepository->findByTimeAndYear($semesterTime, $year);
    }
}

