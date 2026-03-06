<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AdminInterviewSchemaWriteResource;
use App\Entity\InterviewQuestion;
use App\Entity\InterviewQuestionAlternative;
use App\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;

class AdminInterviewSchemaCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AdminInterviewSchemaWriteResource
    {
        \assert($data instanceof AdminInterviewSchemaWriteResource);

        $schema = new InterviewSchema();
        $schema->setName($data->name);

        if ($data->questions !== null) {
            foreach ($data->questions as $questionData) {
                $question = new InterviewQuestion();
                $question->setQuestion($questionData['question'] ?? '');
                $question->setType($questionData['type'] ?? 'text');
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
