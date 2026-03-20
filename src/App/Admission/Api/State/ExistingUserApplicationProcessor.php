<?php

namespace App\Admission\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Admission\Api\Resource\ExistingUserApplicationInput;
use App\Admission\Infrastructure\Entity\AdmissionPeriod;
use App\Identity\Infrastructure\Entity\User;
use App\Admission\Domain\Events\ApplicationCreatedEvent;
use App\Admission\Infrastructure\ApplicationAdmission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ExistingUserApplicationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly ApplicationAdmission $applicationAdmission,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof ExistingUserApplicationInput);

        /** @var User $user */
        $user = $this->security->getUser();

        if (!$user->hasBeenAssistant()) {
            throw new UnprocessableEntityHttpException('Kun eksisterende assistenter kan bruke dette endepunktet.');
        }

        if ($user->getFieldOfStudy() === null) {
            throw new UnprocessableEntityHttpException('Brukeren mangler linje/studieretning.');
        }

        $department = $user->getDepartment();
        $admissionPeriod = $this->em->getRepository(AdmissionPeriod::class)
            ->findOneWithActiveAdmissionByDepartment($department);

        if ($admissionPeriod === null) {
            throw new UnprocessableEntityHttpException('Opptak er ikke åpent for din avdeling.');
        }

        if ($this->applicationAdmission->userHasAlreadyApplied($user)) {
            throw new UnprocessableEntityHttpException('Du har allerede søkt i denne opptaksperioden.');
        }

        $application = $this->applicationAdmission->createApplicationForExistingAssistant($user);

        $application->setMonday($data->monday);
        $application->setTuesday($data->tuesday);
        $application->setWednesday($data->wednesday);
        $application->setThursday($data->thursday);
        $application->setFriday($data->friday);
        $application->setTeamInterest($data->teamInterest);
        if ($data->preferredGroup !== null) {
            $application->setPreferredGroup($data->preferredGroup);
        }

        $this->em->persist($application);
        $this->em->flush();

        $this->dispatcher->dispatch(new ApplicationCreatedEvent($application), ApplicationCreatedEvent::NAME);
    }
}
