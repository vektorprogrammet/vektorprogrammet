<?php

namespace App\Organization\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Organization\Api\Resource\TeamApplicationInput;
use App\Organization\Infrastructure\Repository\TeamRepository;
use App\Organization\Infrastructure\Entity\TeamApplication;
use App\Organization\Domain\Events\TeamApplicationCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TeamApplicationProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof TeamApplicationInput);

        $team = $this->teamRepository->find($data->teamId);
        if (!$team) {
            throw new UnprocessableEntityHttpException('Team not found.');
        }

        $application = new TeamApplication();
        $application->setName($data->name);
        $application->setEmail($data->email);
        $application->setPhone($data->phone);
        $application->setFieldOfStudy($data->fieldOfStudy);
        $application->setYearOfStudy($data->yearOfStudy);
        $application->setMotivationText($data->motivationText);
        $application->setBiography($data->biography);
        $application->setTeam($team);

        $this->em->persist($application);
        $this->em->flush();

        $this->dispatcher->dispatch(new TeamApplicationCreatedEvent($application), TeamApplicationCreatedEvent::NAME);
    }
}
