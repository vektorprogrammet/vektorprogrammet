<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Api\Resource\ApplicationInput;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Organization\Infrastructure\Repository\DepartmentRepository;
use App\Organization\Infrastructure\Repository\FieldOfStudyRepository;
use App\Identity\Infrastructure\Repository\RoleRepository;
use App\Identity\Infrastructure\Entity\User;
use App\Admission\Domain\Events\ApplicationCreatedEvent;
use App\Identity\Domain\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApplicationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly DepartmentRepository $departmentRepo,
        private readonly FieldOfStudyRepository $fieldOfStudyRepo,
        private readonly RoleRepository $roleRepo,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RateLimiterFactory $applicationLimiter,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof ApplicationInput);

        $limiter = $this->applicationLimiter->create($this->requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown');
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }

        $department = $this->departmentRepo->find($data->departmentId);
        if ($department === null) {
            throw new UnprocessableEntityHttpException('Department not found.');
        }

        $admissionPeriod = $this->admissionPeriodRepo->findOneWithActiveAdmissionByDepartment($department);
        if ($admissionPeriod === null) {
            throw new UnprocessableEntityHttpException('No active admission period for this department.');
        }

        $fieldOfStudy = $this->fieldOfStudyRepo->find($data->fieldOfStudyId);
        if ($fieldOfStudy === null) {
            throw new UnprocessableEntityHttpException('Field of study not found.');
        }

        // Find or create user (mirrors ApplicationAdmission::setCorrectUser)
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data->email]);
        if ($user === null) {
            $user = new User();
            $user->setEmail($data->email);
            $user->setFirstName($data->firstName);
            $user->setLastName($data->lastName);
            $user->setPhone($data->phone);
            $user->setGender((string) $data->gender);
            $user->setFieldOfStudy($fieldOfStudy);

            $role = $this->roleRepo->findByRoleName(Roles::ASSISTANT);
            $user->addRole($role);
        }

        // Create application
        $application = new Application();
        $application->setUser($user);
        $application->setAdmissionPeriod($admissionPeriod);
        $application->setYearOfStudy($data->yearOfStudy);
        $application->setMonday($data->monday);
        $application->setTuesday($data->tuesday);
        $application->setWednesday($data->wednesday);
        $application->setThursday($data->thursday);
        $application->setFriday($data->friday);
        $application->setSubstitute($data->substitute);
        $application->setLanguage($data->language);
        $application->setDoublePosition($data->doublePosition);
        $application->setPreferredSchool($data->preferredSchool);
        $application->setPreferredGroup($data->preferredGroup);
        $application->setPreviousParticipation($data->previousParticipation);
        $application->setTeamInterest($data->teamInterest);
        $application->setSpecialNeeds($data->specialNeeds ?? '');
        $application->setHeardAboutFrom([]);

        $this->em->persist($application);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            new ApplicationCreatedEvent($application),
            ApplicationCreatedEvent::NAME
        );
    }
}
