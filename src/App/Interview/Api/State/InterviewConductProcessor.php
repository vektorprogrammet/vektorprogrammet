<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\InterviewConductInput;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewScore;
use App\Interview\Domain\Events\InterviewConductedEvent;
use App\Identity\Infrastructure\Entity\User;
use App\Interview\Infrastructure\InterviewManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InterviewConductProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InterviewManager $interviewManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewConductInput);

        $interview = $this->em->getRepository(Interview::class)->find($uriVariables['id'] ?? 0);
        if ($interview === null) {
            throw new NotFoundHttpException('Interview not found.');
        }

        if (!$this->interviewManager->loggedInUserCanSeeInterview($interview)) {
            throw new AccessDeniedHttpException('You do not have access to this interview.');
        }

        $currentUser = $this->security->getUser();
        if ($currentUser instanceof User && $interview->getApplication() !== null
            && $currentUser->getId() === $interview->getApplication()->getUser()?->getId()) {
            throw new AccessDeniedHttpException('Cannot conduct your own interview.');
        }

        // Initialize answer stubs for any questions not yet answered
        $this->interviewManager->initializeInterviewAnswers($interview);

        // Map answers from input to interview answers
        foreach ($data->answers as $answerData) {
            $questionId = $answerData['questionId'];
            $answerValue = $answerData['answer'] ?? null;

            foreach ($interview->getInterviewAnswers() as $interviewAnswer) {
                if ($interviewAnswer->getInterviewQuestion()->getId() === $questionId) {
                    $interviewAnswer->setAnswer($answerValue);
                    break;
                }
            }
        }

        // Handle interview score
        if ($data->interviewScore !== []) {
            $score = $interview->getInterviewScore();
            if ($score === null) {
                $score = new InterviewScore();
                $interview->setInterviewScore($score);
                $this->em->persist($score);
            }

            $score->setExplanatoryPower($data->interviewScore['explanatoryPower']);
            $score->setRoleModel($data->interviewScore['roleModel']);
            $score->setSuitability($data->interviewScore['suitability']);
            $score->setSuitableAssistant($data->interviewScore['suitableAssistant']);
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
