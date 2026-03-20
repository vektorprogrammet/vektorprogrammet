<?php

namespace Tests\AppBundle\Api;

use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminInterviewSchemaWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // ===== POST /api/admin/interview-schemas (create) =====

    public function testCreateSchemaRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Schema']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateSchemaForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Schema']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSchemaSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'API Test Interview Schema',
        ];

        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateSchemaWithQuestionsSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Schema With Questions',
            'questions' => [
                ['question' => 'Why do you want to be an assistant?', 'type' => 'text'],
                ['question' => 'Rate your math skills', 'type' => 'radio', 'helpText' => 'Be honest'],
            ],
        ];

        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateSchemaValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => '',
        ];

        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // ===== PUT /api/admin/interview-schemas/{id} (edit) =====

    public function testEditSchemaRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/interview-schemas/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditSchemaForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/interview-schemas/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSchemaSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $schema = $em->getRepository(InterviewSchema::class)->findAll()[0];

        $payload = [
            'name' => 'Updated Schema Name',
        ];

        $client->request('PUT', '/api/admin/interview-schemas/'.$schema->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($schema->getId(), $data['id']);
    }

    public function testEditSchemaNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/interview-schemas/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditSchemaValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $schema = $em->getRepository(InterviewSchema::class)->findAll()[0];

        $payload = [
            'name' => '',
        ];

        $client->request('PUT', '/api/admin/interview-schemas/'.$schema->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // ===== CRITICAL: Edit replaces (not appends) questions =====

    public function testEditSchemaReplacesQuestionsInsteadOfAppending(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create a schema with 2 questions
        $schema = new InterviewSchema();
        $schema->setName('Replace Test Schema');
        $q1 = new \App\Interview\Infrastructure\Entity\InterviewQuestion();
        $q1->setQuestion('Old Q1');
        $q1->setType('text');
        $em->persist($q1);
        $schema->addInterviewQuestion($q1);
        $q2 = new \App\Interview\Infrastructure\Entity\InterviewQuestion();
        $q2->setQuestion('Old Q2');
        $q2->setType('text');
        $em->persist($q2);
        $schema->addInterviewQuestion($q2);
        $em->persist($schema);
        $em->flush();
        $schemaId = $schema->getId();

        // PUT with 1 new question -- should replace, not append
        $payload = [
            'name' => 'Replace Test Schema',
            'questions' => [
                ['question' => 'New Q1', 'type' => 'radio'],
            ],
        ];

        $client->request('PUT', '/api/admin/interview-schemas/'.$schemaId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);

        // Verify: should have exactly 1 question, not 3
        $em->clear();
        $updated = $em->getRepository(InterviewSchema::class)->find($schemaId);
        $this->assertNotNull($updated);
        $this->assertCount(1, $updated->getInterviewQuestions());
        $newQ = $updated->getInterviewQuestions()->first();
        $this->assertSame('New Q1', $newQ->getQuestion());
    }

    // ===== IMPORTANT: Interview question alternatives are handled =====

    public function testCreateSchemaWithQuestionAlternatives(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Schema With Alternatives',
            'questions' => [
                [
                    'question' => 'Rate motivation',
                    'type' => 'radio',
                    'alternatives' => ['High', 'Medium', 'Low'],
                ],
            ],
        ];

        $client->request('POST', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);

        // Verify alternatives were saved
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->clear();
        $schema = $em->getRepository(InterviewSchema::class)->find($data['id']);
        $this->assertNotNull($schema);
        $questions = $schema->getInterviewQuestions();
        $this->assertCount(1, $questions);
        $alts = $questions->first()->getAlternatives();
        $this->assertCount(3, $alts);
    }

    // ===== DELETE /api/admin/interview-schemas/{id} =====

    public function testDeleteSchemaRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interview-schemas/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteSchemaForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interview-schemas/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSchemaForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interview-schemas/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        // Delete requires TEAM_LEADER (original controller checks this)
        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSchemaSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        // Create a fresh schema to delete (no interviews reference it)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $schema = new InterviewSchema();
        $schema->setName('To Be Deleted');
        $em->persist($schema);
        $em->flush();
        $schemaId = $schema->getId();

        $client->request('DELETE', '/api/admin/interview-schemas/'.$schemaId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deletion
        $em->clear();
        $deleted = $em->getRepository(InterviewSchema::class)->find($schemaId);
        $this->assertNull($deleted);
    }

    public function testDeleteSchemaNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/interview-schemas/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteSchemaWithLinkedInterviewsReturns409(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        // ischema-1 is used by many interviews in fixtures
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $schemas = $em->getRepository(InterviewSchema::class)->findAll();
        // Find a schema that has interviews referencing it
        $schemaWithInterviews = null;
        foreach ($schemas as $schema) {
            $interviews = $em->getRepository(Interview::class)->findBy(['interviewSchema' => $schema]);
            if (count($interviews) > 0) {
                $schemaWithInterviews = $schema;
                break;
            }
        }
        $this->assertNotNull($schemaWithInterviews, 'Fixture should have a schema with linked interviews');

        $client->request('DELETE', '/api/admin/interview-schemas/'.$schemaWithInterviews->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(409);
    }
}
