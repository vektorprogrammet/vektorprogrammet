<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminDepartmentWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/departments (requires ROLE_ADMIN) ---

    public function testCreateDepartmentRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/departments', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Dept']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateDepartmentForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/departments', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Dept']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateDepartmentForbiddenForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/departments', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Dept']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateDepartmentSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Universitetet i Stavanger',
            'shortName' => 'UiS',
            'email' => 'uis@test.com',
            'city' => 'Stavanger',
        ];

        $client->request('POST', '/api/admin/departments', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateDepartmentValidationRejectsBlankFields(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $payload = [
            'name' => '',
            'shortName' => '',
            'email' => '',
            'city' => '',
        ];

        $client->request('POST', '/api/admin/departments', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- PUT /api/admin/departments/{id} (requires ROLE_TEAM_LEADER) ---

    public function testUpdateDepartmentRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/departments/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateDepartmentForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/departments/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateDepartmentSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'NTNU']);

        $payload = [
            'name' => 'NTNU Updated',
            'shortName' => 'NTNU',
            'email' => 'updated@ntnu.no',
            'city' => 'Trondheim',
        ];

        $client->request('PUT', '/api/admin/departments/'.$dept->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testUpdateDepartmentNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/departments/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'No Dept', 'shortName' => 'XX', 'email' => 'x@x.com', 'city' => 'Nowhere']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateDepartmentValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = $em->getRepository(Department::class)->findOneBy(['shortName' => 'NTNU']);

        $payload = [
            'name' => '',
            'shortName' => '',
            'email' => '',
            'city' => '',
        ];

        $client->request('PUT', '/api/admin/departments/'.$dept->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- DELETE /api/admin/departments/{id} (requires ROLE_ADMIN) ---

    public function testDeleteDepartmentRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/departments/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteDepartmentForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/departments/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteDepartmentForbiddenForTeamLeader(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/departments/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteDepartmentSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        // Create a new department to delete (avoid FK issues with existing ones)
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $dept = new Department();
        $dept->setName('Temp Department');
        $dept->setShortName('TMP');
        $dept->setEmail('tmp@test.com');
        $dept->setCity('TempCity-'.uniqid());
        $em->persist($dept);
        $em->flush();
        $deptId = $dept->getId();

        $client->request('DELETE', '/api/admin/departments/'.$deptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $em->clear();
        $deleted = $em->getRepository(Department::class)->find($deptId);
        $this->assertNull($deleted);
    }

    public function testDeleteDepartmentNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/departments/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
