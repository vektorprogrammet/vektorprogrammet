<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AdminSchedulingSchoolResource;
use App\Entity\Repository\SchoolCapacityRepository;
use App\Shared\Repository\SemesterRepository;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

class AdminSchedulingSchoolProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly SchoolCapacityRepository $schoolCapacityRepo,
        private readonly SemesterRepository $semesterRepo,
    ) {
    }

    /**
     * @return AdminSchedulingSchoolResource[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            return [];
        }

        $department = $user->getDepartment();
        $currentSemester = $this->semesterRepo->findOrCreateCurrentSemester();

        $schoolCapacities = $this->schoolCapacityRepo
            ->findByDepartmentAndSemester($department, $currentSemester);

        $results = [];
        foreach ($schoolCapacities as $sc) {
            $resource = new AdminSchedulingSchoolResource();
            $resource->id = $sc->getId();
            $resource->name = $sc->getSchool()->getName();

            $capacityDays = [
                'Monday' => $sc->getMonday(),
                'Tuesday' => $sc->getTuesday(),
                'Wednesday' => $sc->getWednesday(),
                'Thursday' => $sc->getThursday(),
                'Friday' => $sc->getFriday(),
            ];

            $resource->capacity = [
                1 => $capacityDays,
                2 => $capacityDays,
            ];

            $results[] = $resource;
        }

        return $results;
    }
}
