<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Api\Resource\AdminApplicationCreateInput;
use App\Admission\Infrastructure\Entity\Application;
use App\Admission\Infrastructure\Repository\AdmissionPeriodRepository;
use App\Organization\Infrastructure\Repository\FieldOfStudyRepository;
use App\Entity\Repository\UserRepository;
use App\Entity\User;
use App\Admission\Domain\Events\ApplicationCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class AdminApplicationCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepo,
        private readonly AdmissionPeriodRepository $admissionPeriodRepo,
        private readonly FieldOfStudyRepository $fieldOfStudyRepo,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        assert($data instanceof AdminApplicationCreateInput);

        $admissionPeriod = null;
        if ($data->admissionPeriodId !== null) {
            $admissionPeriod = $this->admissionPeriodRepo->find($data->admissionPeriodId);
            if ($admissionPeriod === null) {
                throw new UnprocessableEntityHttpException('Admission period not found.');
            }
        }

        $fieldOfStudy = null;
        if ($data->fieldOfStudyId !== null) {
            $fieldOfStudy = $this->fieldOfStudyRepo->find($data->fieldOfStudyId);
            if ($fieldOfStudy === null) {
                throw new UnprocessableEntityHttpException('Field of study not found.');
            }
        }

        // Find existing user by email, or create a new one
        $user = $this->userRepo->findUserByEmail($data->email);
        if ($user === null) {
            $user = new User();
            $user->setEmail($data->email);
            $user->setFirstName($data->firstName);
            $user->setLastName($data->lastName);
            $user->setPhone($data->phone);
            $user->setGender(0);
            if ($fieldOfStudy !== null) {
                $user->setFieldOfStudy($fieldOfStudy);
            }
        }

        $application = new Application();
        $application->setUser($user);
        $application->setYearOfStudy($data->yearOfStudy);
        $application->setHeardAboutFrom([]);

        if ($admissionPeriod !== null) {
            $application->setAdmissionPeriod($admissionPeriod);
        }

        $this->em->persist($application);
        $this->em->flush();

        $this->eventDispatcher->dispatch(
            new ApplicationCreatedEvent($application),
            ApplicationCreatedEvent::NAME
        );

        return ['id' => $application->getId()];
    }
}
