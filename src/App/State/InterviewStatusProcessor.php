<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\InterviewStatusInput;
use App\Entity\Interview;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class InterviewStatusProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        assert($data instanceof InterviewStatusInput);

        $interview = $this->em->getRepository(Interview::class)->find($uriVariables['id'] ?? 0);
        if (!$interview) {
            throw new NotFoundHttpException('Interview not found.');
        }

        try {
            $interview->setStatus($data->status);
        } catch (\InvalidArgumentException) {
            throw new UnprocessableEntityHttpException('Invalid status value. Must be between 0 and 4.');
        }

        $this->em->flush();
    }
}
