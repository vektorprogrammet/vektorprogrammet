<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\InterviewConductInput;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewScore;
use App\Interview\Domain\Events\InterviewConductedEvent;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewConductProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InterviewManager $interviewManager,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewConductInput);

        $interview = $this->em->getRepository(Interview::class)->find($uriVariables['id'] ?? 0);
        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        // Initialize answer stubs for any questions not yet answered
        $this->interviewManager->initializeInterviewAnswers($interview);

        // Map answers from input to interview answers
        foreach ($data->answers as $answerData) {
            $questionId = $answerData['questionId'] ?? null;
            $answerValue = $answerData['answer'] ?? null;

            if ($questionId === null) {
                continue;
            }

            foreach ($interview->getInterviewAnswers() as $interviewAnswer) {
                if ($interviewAnswer->getInterviewQuestion()->getId() === $questionId) {
                    $interviewAnswer->setAnswer($answerValue);
                    break;
                }
            }
        }

        // Handle interview score
        if (!empty($data->interviewScore)) {
            $score = $interview->getInterviewScore();
            if (!$score) {
                $score = new InterviewScore();
                $interview->setInterviewScore($score);
                $this->em->persist($score);
            }

            $score->setExplanatoryPower($data->interviewScore['explanatoryPower'] ?? 0);
            $score->setRoleModel($data->interviewScore['roleModel'] ?? 0);
            $score->setSuitability($data->interviewScore['suitability'] ?? 0);
            $score->setSuitableAssistant($data->interviewScore['suitableAssistant'] ?? '');
        }

        $interview->setInterviewed(true);
        $interview->setConducted(new \DateTime());

        $this->em->flush();

        $application = $interview->getApplication();
        if ($application) {
            $this->eventDispatcher->dispatch(
                new InterviewConductedEvent($application),
                InterviewConductedEvent::NAME
            );
        }
    }
}
