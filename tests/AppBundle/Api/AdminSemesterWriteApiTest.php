<?php

namespace Tests\AppBundle\Api;

use App\Shared\Entity\Semester;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminSemesterWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/semesters (requires ROLE_ADMIN) ---

    public function testCreateSemesterRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/semesters', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['semesterTime' => 'Vår', 'year' => '2099']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateSemesterForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['semesterTime' => 'Vår', 'year' => '2099']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSemesterForbiddenForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['semesterTime' => 'Vår', 'year' => '2099']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSemesterSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $payload = [
            'semesterTime' => 'Høst',
            'year' => '2099',
        ];

        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateSemesterValidationRejectsBlankFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $payload = [
            'semesterTime' => '',
            'year' => '',
        ];

        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateSemesterDuplicateReturns409(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // The fixture already created 'Vår 2013' -- attempt to duplicate
        $payload = [
            'semesterTime' => 'Vår',
            'year' => '2013',
        ];

        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(409);
    }

    public function testCreateSemesterValidationRejectsInvalidSemesterTime(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $payload = [
            'semesterTime' => 'Summer',
            'year' => '2099',
        ];

        $client->request('POST', '/api/admin/semesters', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/semesters/{id} (requires ROLE_ADMIN) ---

    public function testDeleteSemesterRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/semesters/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteSemesterForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/semesters/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSemesterForbiddenForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/semesters/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSemesterSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Create a fresh semester to delete (no FK deps)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = new Semester();
        $semester->setSemesterTime('Høst');
        $semester->setYear('2098');
        $em->persist($semester);
        $em->flush();
        $semId = $semester->getId();

        $client->request('DELETE', '/api/admin/semesters/'.$semId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(Semester::class)->find($semId);
        $this->assertNull($deleted);
    }

    public function testDeleteSemesterNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/semesters/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
