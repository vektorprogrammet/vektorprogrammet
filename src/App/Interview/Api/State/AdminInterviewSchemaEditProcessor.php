<?php

namespace App\Interview\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Interview\Api\Resource\AdminInterviewSchemaWriteResource;
use App\Interview\Infrastructure\Entity\InterviewQuestion;
use App\Interview\Infrastructure\Entity\InterviewQuestionAlternative;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AdminInterviewSchemaEditProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminInterviewSchemaWriteResource
    {
        \assert($data instanceof AdminInterviewSchemaWriteResource);

        $id = $uriVariables['id'] ?? null;
        $schema = $id !== null ? $this->em->getRepository(InterviewSchema::class)->find($id) : null;

        if ($schema === null) {
            throw new NotFoundHttpException('Interview schema not found.');
        }

        if ($data->name !== null && $data->name !== '') {
            $schema->setName($data->name);
        } elseif ($data->name === '') {
            throw new UnprocessableEntityHttpException('Name cannot be blank.');
        }

        if ($data->questions !== null) {
            // Clear existing questions (ManyToMany: unlinks from join table)
            $schema->getInterviewQuestions()->clear();

            // Add new questions
            foreach ($data->questions as $questionData) {
                $question = new InterviewQuestion();
                $question->setQuestion($questionData['question']);
                $question->setType($questionData['type']);
                if (isset($questionData['helpText'])) {
                    $question->setHelp($questionData['helpText']);
                }

                if (isset($questionData['alternatives'])) {
                    foreach ($questionData['alternatives'] as $altText) {
                        $alt = new InterviewQuestionAlternative();
                        $alt->setAlternative($altText);
                        $question->addAlternative($alt);
                    }
                }

                $this->em->persist($question);
                $schema->addInterviewQuestion($question);
            }
        }

        $this->em->persist($schema);
        $this->em->flush();

        $result = new AdminInterviewSchemaWriteResource();
        $result->id = $schema->getId();
        $result->name = $schema->getName();

        return $result;
    }
}
