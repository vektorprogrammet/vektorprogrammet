<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class MailingListApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetMailingListRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetMailingListReturnsAssistants(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?type=assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('assistants', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertIsArray($data['users']);
        $this->assertArrayHasKey('count', $data);
        $this->assertIsInt($data['count']);
        $this->assertSame(count($data['users']), $data['count']);
    }

    public function testGetMailingListReturnsTeamMembers(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?type=team', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('team', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertIsArray($data['users']);
    }

    public function testGetMailingListReturnsAllUsers(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?type=all', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('all', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertIsArray($data['users']);
        $this->assertArrayHasKey('count', $data);
    }

    public function testGetMailingListDefaultsToAssistants(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame('assistants', $data['type']);
    }

    public function testGetMailingListUserHasNameAndEmail(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?type=assistants', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        if (count($data['users']) > 0) {
            $user = $data['users'][0];
            $this->assertArrayHasKey('name', $user);
            $this->assertArrayHasKey('email', $user);
            $this->assertNotEmpty($user['name']);
            $this->assertNotEmpty($user['email']);
        }
    }

    public function testGetMailingListWithNonExistentDepartmentReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('assistants', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertEmpty($data['users']);
        $this->assertArrayHasKey('count', $data);
        $this->assertSame(0, $data['count']);
    }

    public function testGetMailingListWithNonExistentSemesterReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('assistants', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertEmpty($data['users']);
        $this->assertArrayHasKey('count', $data);
        $this->assertSame(0, $data['count']);
    }

    public function testGetMailingListWithInvalidTypeReturnsEmptyUsers(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/mailing-lists?type=invalidtype', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('type', $data);
        $this->assertSame('invalidtype', $data['type']);
        $this->assertArrayHasKey('users', $data);
        $this->assertEmpty($data['users']);
        $this->assertArrayHasKey('count', $data);
        $this->assertSame(0, $data['count']);
    }
}
