<?php

namespace Tests\AppBundle\Api;

use App\Organization\Infrastructure\Entity\FieldOfStudy;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminUserWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- POST /api/admin/users (create user) ---

    public function testCreateUserRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/users', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['firstName' => 'Test']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateUserForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['firstName' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateUserForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['firstName' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateUserSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fieldOfStudy = $em->getRepository(FieldOfStudy::class)->findAll()[0];

        $payload = [
            'firstName' => 'Nyansen',
            'lastName' => 'Testsen',
            'email' => 'nyansen-testsen-unique@example.com',
            'phone' => '99998877',
            'fieldOfStudyId' => $fieldOfStudy->getId(),
        ];

        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateUserValidationRejectsBlankFirstName(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fieldOfStudy = $em->getRepository(FieldOfStudy::class)->findAll()[0];

        $payload = [
            'firstName' => '',
            'lastName' => 'Testsen',
            'email' => 'blank-test@example.com',
            'phone' => '99998877',
            'fieldOfStudyId' => $fieldOfStudy->getId(),
        ];

        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateUserValidationRejectsBlankEmail(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'firstName' => 'Test',
            'lastName' => 'Testsen',
            'email' => '',
            'phone' => '99998877',
        ];

        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateUserValidationRejectsInvalidEmail(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $payload = [
            'firstName' => 'Test',
            'lastName' => 'Testsen',
            'email' => 'not-an-email',
            'phone' => '99998877',
        ];

        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateUserAdminCanSpecifyFieldOfStudy(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fieldOfStudy = $em->getRepository(FieldOfStudy::class)->findAll()[0];

        $payload = [
            'firstName' => 'Admin',
            'lastName' => 'Created',
            'email' => 'admin-created-unique-fos@example.com',
            'phone' => '11223344',
            'fieldOfStudyId' => $fieldOfStudy->getId(),
        ];

        $client->request('POST', '/api/admin/users', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    // --- DELETE /api/admin/users/{id} ---

    public function testDeleteUserRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/users/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteUserForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/users/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteUserSuccess(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Find a user to delete -- use 'ingrid' (role-1, fos-1, no FK dependencies)
        $userToDelete = $em->getRepository(User::class)->findOneBy(['user_name' => 'ingrid']);
        $this->assertNotNull($userToDelete, 'Fixture user "ingrid" must exist');
        $userId = $userToDelete->getId();

        $client->request('DELETE', '/api/admin/users/'.$userId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deleted
        $em->clear();
        $deleted = $em->getRepository(User::class)->find($userId);
        $this->assertNull($deleted);
    }

    public function testDeleteUserCannotDeleteSelf(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $adminUser = $em->getRepository(User::class)->findOneBy(['user_name' => 'admin']);
        $this->assertNotNull($adminUser);

        $client->request('DELETE', '/api/admin/users/'.$adminUser->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(409);
    }

    public function testDeleteUserNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('admin', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/users/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteUserTeamLeaderCanDeleteSameDepartment(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // Create a fresh user in dep-1 (same dept as teamleader) to avoid FK cascade issues with fixtures
        $fos = $em->getRepository(FieldOfStudy::class)->findAll()[0]; // fos-1 -> dep-1
        $freshUser = new User();
        $freshUser->setFirstName('Deletable');
        $freshUser->setLastName('User');
        $freshUser->setEmail('deletable-user-test@example.com');
        $freshUser->setPhone('99990000');
        $freshUser->setUserName('deletable-user-test');
        $freshUser->setPassword('test');
        $freshUser->setGender(0);
        $freshUser->setFieldOfStudy($fos);
        $em->persist($freshUser);
        $em->flush();
        $userId = $freshUser->getId();

        $client->request('DELETE', '/api/admin/users/'.$userId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteUserTeamLeaderCannotDeleteDifferentDepartment(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);

        // user-3 (kribo) is fos-3 (dep-2, UiB), different dept from teamleader (dep-1)
        $userToDelete = $em->getRepository(User::class)->findOneBy(['user_name' => 'kribo']);
        $this->assertNotNull($userToDelete);

        $client->request('DELETE', '/api/admin/users/'.$userToDelete->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    // --- POST /api/admin/users/{id}/activation ---

    public function testSendActivationRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/users/1/activation', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSendActivationForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/users/1/activation', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSendActivationSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'assistent']);
        $this->assertNotNull($user);

        $client->request('POST', '/api/admin/users/'.$user->getId().'/activation', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);
    }

    public function testSendActivationNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $client = static::createClient();

        $client->request('POST', '/api/admin/users/99999/activation', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }
}
