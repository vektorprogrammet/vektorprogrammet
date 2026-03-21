<?php

namespace Tests\AppBundle\Api;

use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\Interview;
use App\Interview\Infrastructure\Entity\InterviewSchema;
use App\Identity\Infrastructure\Entity\User;
use Tests\BaseWebTestCase;

class AdminInterviewWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Assign ---

    public function testAssignRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'applicationId' => 1,
            'interviewerId' => 1,
            'interviewSchemaId' => 1,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAssignForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'applicationId' => 1,
            'interviewerId' => 1,
            'interviewSchemaId' => 1,
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAssignInterviewerToApplication(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $application = $em->getRepository(Application::class)->findOneBy(['interview' => null]);
        $this->assertNotNull($application, 'Fixture must have an application without interview');

        $interviewer = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $schema = $em->getRepository(InterviewSchema::class)->findOneBy([]);

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'applicationId' => $application->getId(),
            'interviewerId' => $interviewer->getId(),
            'interviewSchemaId' => $schema->getId(),
        ]));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testAssignApplicationNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'applicationId' => 99999,
            'interviewerId' => 1,
            'interviewSchemaId' => 1,
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAssignValidationRejectsZeroIds(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'applicationId' => 0,
            'interviewerId' => 0,
            'interviewSchemaId' => 0,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Bulk Assign ---

    public function testBulkAssignRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/bulk-assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'assignments' => [],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testBulkAssignForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/bulk-assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'assignments' => [],
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testBulkAssignEmptyAssignments(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/bulk-assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'assignments' => [],
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testBulkAssignWithOneAssignment(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $application = $em->getRepository(Application::class)->findOneBy(['interview' => null]);
        $this->assertNotNull($application, 'Fixture must have an application without interview');

        $interviewer = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $schema = $em->getRepository(InterviewSchema::class)->findOneBy([]);

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/bulk-assign', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'assignments' => [
                [
                    'applicationId' => $application->getId(),
                    'interviewerId' => $interviewer->getId(),
                    'interviewSchemaId' => $schema->getId(),
                ],
            ],
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    // --- Schedule ---

    public function testScheduleRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/1/schedule', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'datetime' => '2026-04-01T10:00:00+02:00',
            'room' => 'D1-123',
            'campus' => 'Gløshaugen',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testScheduleInterview(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview, 'Fixture must have interview with responseCode=code');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/'.$interview->getId().'/schedule', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'datetime' => '2026-04-01T10:00:00+02:00',
            'room' => 'A1-100',
            'campus' => 'Dragvoll',
            'mapLink' => 'https://maps.example.com',
            'from' => 'interviewer@example.com',
            'to' => 'applicant@example.com',
            'message' => 'Looking forward to meeting you!',
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testScheduleInterviewNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/99999/schedule', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'datetime' => '2026-04-01T10:00:00+02:00',
            'room' => 'D1-123',
            'campus' => 'Gløshaugen',
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testScheduleEmptyDatetimeReturns422(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview);

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/'.$interview->getId().'/schedule', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'datetime' => '',
            'room' => 'D1-123',
            'campus' => 'Gløshaugen',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testScheduleInvalidDatetimeReturns422(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview);

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/'.$interview->getId().'/schedule', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'datetime' => 'not-a-date',
            'room' => 'D1-123',
            'campus' => 'Gløshaugen',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Conduct ---

    public function testConductRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/1/conduct', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'answers' => [],
            'interviewScore' => [
                'explanatoryPower' => 5,
                'roleModel' => 4,
                'suitability' => 6,
                'suitableAssistant' => 'Ja',
            ],
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testConductInterview(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        // Use interview5 (responseCode=code) which has ischema-1 (2 questions)
        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview, 'Fixture must have interview with responseCode=code');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/'.$interview->getId().'/conduct', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'answers' => [],
            'interviewScore' => [
                'explanatoryPower' => 5,
                'roleModel' => 4,
                'suitability' => 6,
                'suitableAssistant' => 'Ja',
            ],
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testConductInterviewWithAnswers(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        // interview5 (responseCode=code) uses ischema-1 which has 2 questions (iq-1, iq-2)
        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview, 'Fixture must have interview with responseCode=code');

        $schema = $interview->getInterviewSchema();
        $this->assertNotNull($schema);
        $questions = $schema->getInterviewQuestions();
        $this->assertGreaterThanOrEqual(2, $questions->count(), 'Schema must have at least 2 questions');

        $questionIds = [];
        foreach ($questions as $question) {
            $questionIds[] = $question->getId();
        }

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/'.$interview->getId().'/conduct', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'answers' => [
                ['questionId' => $questionIds[0], 'answer' => 'Motivert for vektorprogrammet'],
                ['questionId' => $questionIds[1], 'answer' => 'Erfaring som privatlærer'],
            ],
            'interviewScore' => [
                'explanatoryPower' => 6,
                'roleModel' => 5,
                'suitability' => 7,
                'suitableAssistant' => 'Ja',
            ],
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testConductInterviewNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/interviews/99999/conduct', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'answers' => [],
            'interviewScore' => [
                'explanatoryPower' => 5,
                'roleModel' => 4,
                'suitability' => 6,
                'suitableAssistant' => 'Ja',
            ],
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Status Update ---

    public function testStatusUpdateRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/1/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'status' => 1,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testStatusUpdateForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/1/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'status' => 1,
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testStatusUpdateInterview(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview, 'Fixture must have interview with responseCode=code');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interview->getId().'/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'status' => 1,
        ]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testStatusUpdateInterviewNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/99999/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'status' => 1,
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testStatusUpdateInvalidStatus(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->findByResponseCode('code');
        $this->assertNotNull($interview);

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interview->getId().'/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode([
            'status' => 99,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }
}
