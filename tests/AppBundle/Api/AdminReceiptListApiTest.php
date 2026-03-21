<?php

declare(strict_types=1);

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminReceiptListApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testListRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testListForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testListAllowedForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);
    }

    public function testListReturnsDepartmentScopedReceipts(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $members = $data['hydra:member'];

        foreach ($members as $receipt) {
            $this->assertArrayHasKey('id', $receipt);
            $this->assertArrayHasKey('visualId', $receipt);
            $this->assertArrayHasKey('userName', $receipt);
            $this->assertArrayHasKey('description', $receipt);
            $this->assertArrayHasKey('sum', $receipt);
            $this->assertArrayHasKey('status', $receipt);
            $this->assertContains($receipt['status'], ['pending', 'refunded', 'rejected']);
        }
    }

    public function testListFiltersByStatus(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts?status=pending', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $members = $data['hydra:member'];

        foreach ($members as $receipt) {
            $this->assertSame('pending', $receipt['status']);
        }
    }
}
