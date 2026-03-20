<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\Department;
use App\Organization\Infrastructure\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminTeamWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Endpoint 8: POST /api/admin/teams (create) ---

    public function testCreateTeamRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/teams', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Team']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateTeamForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/teams', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test Team']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateTeamSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'name' => 'New API Team',
            'email' => 'new-api-team@vektorprogrammet.no',
            'shortDescription' => 'A brand new team',
            'description' => 'Full description of the team',
            'departmentId' => $department->getId(),
        ];

        $client->request('POST', '/api/admin/teams', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateTeamValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $payload = [
            'name' => '',
            'departmentId' => $department->getId(),
        ];

        $client->request('POST', '/api/admin/teams', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateTeamValidationRejectsInvalidDepartment(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Test Team',
            'email' => 'test-team@vektorprogrammet.no',
            'departmentId' => 99999,
        ];

        $client->request('POST', '/api/admin/teams', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Endpoint 9: PUT /api/admin/teams/{id} (update) ---

    public function testUpdateTeamRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/teams/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateTeamForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/teams/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateTeamSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];

        $payload = [
            'name' => 'Updated Team Name',
            'email' => 'updated@vektorprogrammet.no',
            'shortDescription' => 'Updated short desc',
            'description' => 'Updated full description',
        ];

        $client->request('PUT', '/api/admin/teams/'.$team->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Updated Team Name', $data['name']);
    }

    public function testUpdateTeamNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/teams/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Whatever']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateTeamValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];

        $payload = ['name' => ''];

        $client->request('PUT', '/api/admin/teams/'.$team->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Endpoint 10: DELETE /api/admin/teams/{id} ---

    public function testDeleteTeamRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/teams/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteTeamForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/teams/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteTeamSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Create a team to delete
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        $team = new Team();
        $team->setName('Team to Delete');
        $team->setEmail('delete-me@vektorprogrammet.no');
        $team->setDepartment($department);
        $em->persist($team);
        $em->flush();
        $teamId = $team->getId();

        $client->request('DELETE', '/api/admin/teams/'.$teamId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deleted
        $em->clear();
        $deleted = $em->getRepository(Team::class)->find($teamId);
        $this->assertNull($deleted);
    }

    public function testDeleteTeamPreservesDeletedTeamNameOnMemberships(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $department = $em->getRepository(Department::class)->findAll()[0];

        // Create a team with a membership
        $team = new Team();
        $team->setName('Preserve Name Team');
        $team->setEmail('preserve@vektorprogrammet.no');
        $team->setDepartment($department);
        $em->persist($team);

        $user = $em->getRepository(\App\Identity\Infrastructure\Entity\User::class)->findOneBy(['user_name' => 'admin']);
        $semester = $em->getRepository(\App\Shared\Entity\Semester::class)->findAll()[0];
        $position = $em->getRepository(\App\Organization\Infrastructure\Entity\Position::class)->findOneBy(['name' => 'Medlem']);

        $membership = new \App\Organization\Infrastructure\Entity\TeamMembership();
        $membership->setTeam($team);
        $membership->setUser($user);
        $membership->setStartSemester($semester);
        $membership->setPosition($position);
        $em->persist($membership);
        $em->flush();
        $membershipId = $membership->getId();

        $client->request('DELETE', '/api/admin/teams/'.$team->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify the membership's deletedTeamName was preserved
        $em->clear();
        $orphanedMembership = $em->getRepository(\App\Organization\Infrastructure\Entity\TeamMembership::class)->find($membershipId);
        $this->assertNotNull($orphanedMembership);
        $this->assertSame('Preserve Name Team', $orphanedMembership->getTeamName());
    }

    public function testDeleteTeamNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/teams/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
