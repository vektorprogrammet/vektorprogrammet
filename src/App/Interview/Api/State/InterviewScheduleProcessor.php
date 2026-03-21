<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\InterviewScheduleInput;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Domain\Events\InterviewEvent;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewScheduleProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InterviewManager $interviewManager,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewScheduleInput);

        $interview = $this->em->getRepository(Interview::class)->find($uriVariables['id'] ?? 0);
        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw new AccessDeniedHttpException('You do not have access to this interview.');
        }

        if ($data->datetime === '') {
            throw new UnprocessableEntityHttpException('datetime is required.');
        }

        try {
            $scheduledAt = new \DateTime($data->datetime);
        } catch (\Exception) {
            throw new UnprocessableEntityHttpException('Invalid datetime format.');
        }

        $interview->setScheduled($scheduledAt);
        $interview->setRoom($data->room);
        $interview->setCampus($data->campus);
        $interview->setMapLink($data->mapLink);
        $interview->resetStatus();

        if (!$interview->getResponseCode()) {
            $interview->generateAndSetResponseCode();
        }

        $this->em->flush();

        $scheduleData = [
            'datetime' => $scheduledAt,
            'room' => $data->room,
            'campus' => $data->campus,
            'mapLink' => $data->mapLink,
            'from' => $data->from,
            'to' => $data->to,
            'message' => $data->message,
        ];

        $this->eventDispatcher->dispatch(
            new InterviewEvent($interview, $scheduleData),
            InterviewEvent::SCHEDULE
        );
    }
}
