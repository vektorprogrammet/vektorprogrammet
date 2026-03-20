<?php

namespace Tests\AppBundle\Api;

use App\Survey\Infrastructure\Entity\Survey;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminSurveyWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // ===== POST /api/admin/surveys (create) =====

    public function testCreateSurveyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/surveys', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Survey']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateSurveyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Survey']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSurveySuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];

        $payload = [
            'name' => 'API Test Survey',
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateSurveyWithQuestionsSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];

        $payload = [
            'name' => 'Survey With Questions',
            'semesterId' => $semester->getId(),
            'questions' => [
                ['question' => 'What is your name?', 'type' => 'text'],
                ['question' => 'Rate us', 'type' => 'radio', 'alternatives' => ['Good', 'Bad']],
            ],
        ];

        $client->request('POST', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateSurveyValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];

        $payload = [
            'name' => '',
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateSurveyValidationRejectsMissingSemester(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'No Semester Survey',
        ];

        $client->request('POST', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // ===== PUT /api/admin/surveys/{id} (edit) =====

    public function testEditSurveyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/surveys/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditSurveyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/surveys/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSurveySuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $survey = $em->getRepository(Survey::class)->findAll()[0];

        $payload = [
            'name' => 'Updated Survey Name',
        ];

        $client->request('PUT', '/api/admin/surveys/'.$survey->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($survey->getId(), $data['id']);
    }

    public function testEditSurveyNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/surveys/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditSurveyValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $survey = $em->getRepository(Survey::class)->findAll()[0];

        $payload = [
            'name' => '',
        ];

        $client->request('PUT', '/api/admin/surveys/'.$survey->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // ===== DELETE /api/admin/surveys/{id} =====

    public function testDeleteSurveyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/surveys/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteSurveyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/surveys/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSurveySuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Create a fresh survey to delete (avoid fixture dependencies)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];
        $department = $em->getRepository(\App\Entity\Department::class)->findAll()[0];

        $survey = new Survey();
        $survey->setName('To Be Deleted');
        $survey->setSemester($semester);
        $survey->setDepartment($department);
        $em->persist($survey);
        $em->flush();
        $surveyId = $survey->getId();

        $client->request('DELETE', '/api/admin/surveys/'.$surveyId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deletion
        $em->clear();
        $deleted = $em->getRepository(Survey::class)->find($surveyId);
        $this->assertNull($deleted);
    }

    public function testDeleteSurveyNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/surveys/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    // ===== POST /api/admin/surveys/{id}/copy =====

    public function testCopySurveyRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/surveys/1/copy', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCopySurveyForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/surveys/1/copy', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCopySurveySuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $survey = $em->getRepository(Survey::class)->findAll()[0];
        $originalId = $survey->getId();

        $client->request('POST', '/api/admin/surveys/'.$originalId.'/copy', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        // The copy should have a different ID than the original
        $this->assertNotSame($originalId, $data['id']);
    }

    public function testCopySurveyNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('POST', '/api/admin/surveys/99999/copy', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }

    // ===== CRITICAL: Edit replaces (not appends) questions =====

    public function testEditSurveyReplacesQuestionsInsteadOfAppending(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];
        $department = $em->getRepository(\App\Entity\Department::class)->findAll()[0];

        // Create a survey with 2 questions
        $survey = new Survey();
        $survey->setName('Replace Test Survey');
        $survey->setSemester($semester);
        $survey->setDepartment($department);
        $q1 = new \App\Survey\Infrastructure\Entity\SurveyQuestion();
        $q1->setQuestion('Old Q1');
        $q1->setType('text');
        $q1->setOptional(false);
        $em->persist($q1);
        $survey->addSurveyQuestion($q1);
        $q2 = new \App\Survey\Infrastructure\Entity\SurveyQuestion();
        $q2->setQuestion('Old Q2');
        $q2->setType('text');
        $q2->setOptional(false);
        $em->persist($q2);
        $survey->addSurveyQuestion($q2);
        $em->persist($survey);
        $em->flush();
        $surveyId = $survey->getId();

        // PUT with 1 new question -- should replace, not append
        $payload = [
            'name' => 'Replace Test Survey',
            'questions' => [
                ['question' => 'New Q1', 'type' => 'radio', 'alternatives' => ['Yes', 'No']],
            ],
        ];

        $client->request('PUT', '/api/admin/surveys/'.$surveyId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);

        // Verify: should have exactly 1 question, not 3
        $em->clear();
        $updated = $em->getRepository(Survey::class)->find($surveyId);
        $this->assertNotNull($updated);
        $this->assertCount(1, $updated->getSurveyQuestions());
        $newQ = $updated->getSurveyQuestions()->first();
        $this->assertSame('New Q1', $newQ->getQuestion());
    }

    public function testCopySurveyClonesQuestions(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        // Find a survey that has questions (from fixtures)
        $surveys = $em->getRepository(Survey::class)->findAll();
        $surveyWithQuestions = null;
        foreach ($surveys as $s) {
            if (count($s->getSurveyQuestions()) > 0) {
                $surveyWithQuestions = $s;
                break;
            }
        }
        $this->assertNotNull($surveyWithQuestions, 'Fixture should have a survey with questions');
        $originalQuestionCount = count($surveyWithQuestions->getSurveyQuestions());

        $client->request('POST', '/api/admin/surveys/'.$surveyWithQuestions->getId().'/copy', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);

        // Verify the copy has same number of questions
        $em->clear();
        $copy = $em->getRepository(Survey::class)->find($data['id']);
        $this->assertNotNull($copy);
        $this->assertCount($originalQuestionCount, $copy->getSurveyQuestions());
        // Name should be prefixed with "Kopi av "
        $this->assertStringStartsWith('Kopi av ', $copy->getName());
    }
}
