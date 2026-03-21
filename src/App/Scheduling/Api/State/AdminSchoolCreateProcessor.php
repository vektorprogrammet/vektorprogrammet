<?php

namespace App\Scheduling\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Identity\Infrastructure\AccessControlService;
use App\Identity\Infrastructure\Entity\User;
use App\Organization\Infrastructure\Entity\Department;
use App\Scheduling\Infrastructure\Entity\School;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminSchoolCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly AccessControlService $accessControl,
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        if ($data->departmentId === null) {
            throw new UnprocessableEntityHttpException('departmentId is required.');
        }

        $department = $this->em->getRepository(Department::class)->find($data->departmentId);
        if ($department === null) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $this->accessControl->assertDepartmentAccess($department, $user);

        $school = new School();
        $school->setName($data->name);
        $school->setContactPerson($data->contactPerson);
        $school->setEmail($data->email);
        $school->setPhone($data->phone);
        $school->setInternational($data->international);
        $school->setActive($data->active);

        // Link school and department (ManyToMany, owning side is Department)
        $school->addDepartment($department);
        $department->addSchool($school);

        $this->em->persist($school);
        $this->em->flush();

        return ['id' => $school->getId()];
    }
}
