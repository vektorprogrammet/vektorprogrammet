<?php

namespace Tests\AppBundle\Api;

use App\Entity\Department;
use App\Shared\Entity\Semester;
use App\Content\Infrastructure\Entity\SocialEvent;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminSocialEventWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/social-events (create) ---

    public function testCreateRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/social-events', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Test Event']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Test Event']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'title' => 'New API Event',
            'description' => 'An event created via API',
            'startTime' => '2026-06-15T10:00:00+00:00',
            'endTime' => '2026-06-15T12:00:00+00:00',
            'departmentId' => $department->getId(),
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateValidationRejectsBlankTitle(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'title' => '',
            'description' => 'No title',
            'startTime' => '2026-06-15T10:00:00+00:00',
            'endTime' => '2026-06-15T12:00:00+00:00',
            'departmentId' => $department->getId(),
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateValidationRejectsInvalidDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'title' => 'Test Event',
            'description' => 'desc',
            'startTime' => '2026-06-15T10:00:00+00:00',
            'endTime' => '2026-06-15T12:00:00+00:00',
            'departmentId' => 99999,
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateValidationRejectsInvalidSemester(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'title' => 'Test Event',
            'description' => 'desc',
            'startTime' => '2026-06-15T10:00:00+00:00',
            'endTime' => '2026-06-15T12:00:00+00:00',
            'departmentId' => $department->getId(),
            'semesterId' => 99999,
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateValidationRejectsNullStartTime(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'title' => 'Missing Times Event',
            'description' => 'No start/end time',
            'departmentId' => $department->getId(),
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateValidationRejectsTitleTooLong(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'title' => str_repeat('A', 256),
            'description' => 'desc',
            'startTime' => '2026-06-15T10:00:00+00:00',
            'endTime' => '2026-06-15T12:00:00+00:00',
            'departmentId' => $department->getId(),
            'semesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/social-events', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/social-events/{id} (edit) ---

    public function testEditRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/social-events/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/social-events/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $event = $em->getRepository(SocialEvent::class)->findAll()[0];

        $payload = [
            'title' => 'Updated Event Title',
            'description' => 'Updated description',
            'startTime' => '2026-07-01T14:00:00+00:00',
            'endTime' => '2026-07-01T16:00:00+00:00',
        ];

        $client->request('PUT', '/api/admin/social-events/'.$event->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated Event Title', $data['title']);
    }

    public function testEditNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/social-events/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => 'Whatever']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditValidationRejectsBlankTitle(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $event = $em->getRepository(SocialEvent::class)->findAll()[0];

        $client->request('PUT', '/api/admin/social-events/'.$event->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['title' => '']));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testEditRejectsInvalidDepartmentId(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $event = $em->getRepository(SocialEvent::class)->findAll()[0];

        $payload = [
            'title' => 'Valid Title',
            'departmentId' => 99999,
        ];

        $client->request('PUT', '/api/admin/social-events/'.$event->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testEditRejectsInvalidSemesterId(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $event = $em->getRepository(SocialEvent::class)->findAll()[0];

        $payload = [
            'title' => 'Valid Title',
            'semesterId' => 99999,
        ];

        $client->request('PUT', '/api/admin/social-events/'.$event->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/social-events/{id} ---

    public function testDeleteRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/social-events/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/social-events/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $event = new SocialEvent();
        $event->setTitle('Event To Delete');
        $event->setDescription('Will be deleted');
        $event->setStartTime(new \DateTime('+1 day'));
        $event->setEndTime(new \DateTime('+1 day 1 hour'));
        $event->setDepartment($department);
        $event->setSemester($semester);
        $em->persist($event);
        $em->flush();
        $eventId = $event->getId();

        $client->request('DELETE', '/api/admin/social-events/'.$eventId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(SocialEvent::class)->find($eventId);
        $this->assertNull($deleted);
    }

    public function testDeleteNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/social-events/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
