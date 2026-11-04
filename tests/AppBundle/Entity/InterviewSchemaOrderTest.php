<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\InterviewQuestion;
use AppBundle\Entity\InterviewSchema;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class InterviewSchemaOrderTest extends TestCase
{
    private function question(int $id): InterviewQuestion
    {
        $question = new InterviewQuestion();
        $property = new ReflectionProperty(InterviewQuestion::class, 'id');
        $property->setValue($question, $id);
        return $question;
    }

    public function testOrderIsScopedToSchemaAndKeepsQuestionIdentity(): void
    {
        $first = $this->question(10);
        $second = $this->question(20);
        $schema = new InterviewSchema();
        $other = new InterviewSchema();
        foreach ([$schema, $other] as $item) {
            $item->addInterviewQuestion($first);
            $item->addInterviewQuestion($second);
        }
        $schema->setQuestionOrder([$second, $first]);
        self::assertSame([$second, $first], array_values($schema->getInterviewQuestions()->toArray()));
        self::assertSame([$first, $second], array_values($other->getInterviewQuestions()->toArray()));

        // Simulate rehydration of the persisted JSON without changing association order.
        $property = new ReflectionProperty(InterviewSchema::class, 'questionOrder');
        $property->setValue($schema, json_decode(json_encode([20, 10]), true));
        self::assertSame([$second, $first], array_values($schema->getInterviewQuestions()->toArray()));

        $schema->removeInterviewQuestion($second);
        $new = $this->question(30);
        $schema->addInterviewQuestion($new);
        self::assertSame([$first, $new], array_values($schema->getInterviewQuestions()->toArray()));
    }
}
