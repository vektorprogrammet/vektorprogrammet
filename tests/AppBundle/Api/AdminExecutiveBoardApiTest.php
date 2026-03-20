<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\ExecutiveBoard;
use App\Organization\Infrastructure\Entity\ExecutiveBoardMembership;
use App\Shared\Entity\Semester;
use App\Identity\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminExecutiveBoardApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- Endpoint 14: POST /api/admin/executive-board/members (add member) ---

    public function testAddBoardMemberRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/executive-board/members', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAddBoardMemberForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/executive-board/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 1]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAddBoardMemberSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'positionTitle' => 'Styremedlem',
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/executive-board/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testAddBoardMemberValidationRejectsMissingUser(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'positionTitle' => 'Styremedlem',
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/executive-board/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testAddBoardMemberValidationRejectsMissingPosition(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $payload = [
            'userId' => $user->getId(),
            'positionTitle' => '',
            'startSemesterId' => $semester->getId(),
        ];

        $client->request('POST', '/api/admin/executive-board/members', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // --- Endpoint 15: PUT /api/admin/executive-board/members/{id} (update) ---

    public function testUpdateBoardMemberRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/executive-board/members/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateBoardMemberForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/executive-board/members/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateBoardMemberSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $membership = $em->getRepository(ExecutiveBoardMembership::class)->findAll()[0];
        $endSemester = $em->getRepository(Semester::class)->findAll()[1];

        $payload = [
            'positionTitle' => 'Nestleder',
            'endSemesterId' => $endSemester->getId(),
        ];

        $client->request('PUT', '/api/admin/executive-board/members/'.$membership->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testUpdateBoardMemberNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/executive-board/members/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['positionTitle' => 'Nestleder']));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Endpoint 16: DELETE /api/admin/executive-board/members/{id} ---

    public function testDeleteBoardMemberRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/executive-board/members/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteBoardMemberForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/executive-board/members/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteBoardMemberSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        // Create a membership to delete
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $board = $em->getRepository(ExecutiveBoard::class)->findAll()[0];
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $semester = $em->getRepository(Semester::class)->findAll()[0];

        $membership = new ExecutiveBoardMembership();
        $membership->setBoard($board);
        $membership->setUser($user);
        $membership->setPositionName('Temp Member');
        $membership->setStartSemester($semester);
        $em->persist($membership);
        $em->flush();
        $membershipId = $membership->getId();

        $client->request('DELETE', '/api/admin/executive-board/members/'.$membershipId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deleted
        $em->clear();
        $deleted = $em->getRepository(ExecutiveBoardMembership::class)->find($membershipId);
        $this->assertNull($deleted);
    }

    public function testDeleteBoardMemberNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/executive-board/members/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Endpoint 17: PUT /api/admin/executive-board (update board) ---

    public function testUpdateBoardRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/executive-board', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated Board']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUpdateBoardForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/executive-board', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated Board']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUpdateBoardSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Hovedstyret Updated',
            'description' => 'Updated board description',
            'shortDescription' => 'Updated short',
        ];

        $client->request('PUT', '/api/admin/executive-board', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame('Hovedstyret Updated', $data['name']);
    }

    public function testUpdateBoardValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = ['name' => ''];

        $client->request('PUT', '/api/admin/executive-board', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }
}
