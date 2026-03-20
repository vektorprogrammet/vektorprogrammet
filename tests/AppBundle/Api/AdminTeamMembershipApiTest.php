<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\Position;
use App\Shared\Entity\Semester;
use App\Organization\Infrastructure\Entity\Team;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminTeamMembershipApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Endpoint 11: POST /api/admin/teams/{id}/members (add member) ---

    public function testAddMemberRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/teams/1/members', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddMemberForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/teams/1/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAddMemberSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/teams/'.$team->getId().'/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testAddMemberWithPosition(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'nmbu']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];
        $position = $em->getRepository(Position::class)->findOneBy(['name' => 'Leder']);

        $payload = [
            'userId' => $user->getId(),
            'startSemesterId' => $semester->getId(),
            'positionId' => $position->getId(),
        ];

        $client->request('POST', '/api/admin/teams/'.$team->getId().'/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
    }

    public function testAddMemberValidationRejectsMissingUser(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/teams/'.$team->getId().'/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testAddMemberTeamNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/teams/99999/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Endpoint 12: PUT /api/admin/team-memberships/{id} (update) ---

    public function testUpdateMembershipRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/team-memberships/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateMembershipForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/team-memberships/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateMembershipSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $membership = $em->getRepository(TeamMembership::class)->findAll()[0];
        $newPosition = $em->getRepository(Position::class)->findOneBy(['name' => 'Leder']);
        $endSemester = $em->getRepository(Semester::class)->findAll()[1];

        $payload = [
            'positionId' => $newPosition->getId(),
            'endSemesterId' => $endSemester->getId(),
        ];

        $client->request('PUT', '/api/admin/team-memberships/'.$membership->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testUpdateMembershipNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/team-memberships/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['positionId' => 1]));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Endpoint 13: DELETE /api/admin/team-memberships/{id} ---

    public function testDeleteMembershipRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/team-memberships/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteMembershipForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/team-memberships/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteMembershipSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        // Create a membership to delete
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $team = $em->getRepository(Team::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];
        $position = $em->getRepository(Position::class)->findOneBy(['name' => 'Medlem']);

        $membership = new TeamMembership();
        $membership->setTeam($team);
        $membership->setUser($user);
        $membership->setStartSemester($semester);
        $membership->setPosition($position);
        $em->persist($membership);
        $em->flush();
        $membershipId = $membership->getId();

        $client->request('DELETE', '/api/admin/team-memberships/'.$membershipId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deleted
        $em->clear();
        $deleted = $em->getRepository(TeamMembership::class)->find($membershipId);
        $this->assertNull($deleted);
    }

    public function testDeleteMembershipNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/team-memberships/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
