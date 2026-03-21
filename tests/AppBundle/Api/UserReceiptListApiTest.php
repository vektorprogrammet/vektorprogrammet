<?php

declare(strict_types=1);

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class UserReceiptListApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testListRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/my/receipts', [], [], [
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testListAllowedForAuthenticatedUser(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/my/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('hydra:member', $data);
        $this->assertIsArray($data['hydra:member']);
    }

    public function testListReturnsCorrectShape(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/my/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/ld+json',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $members = $data['hydra:member'];

        foreach ($members as $receipt) {
            $this->assertArrayHasKey('id', $receipt);
            $this->assertArrayHasKey('visualId', $receipt);
            $this->assertArrayHasKey('description', $receipt);
            $this->assertArrayHasKey('sum', $receipt);
            $this->assertArrayHasKey('status', $receipt);
            $this->assertContains($receipt['status'], ['pending', 'refunded', 'rejected']);
            // userName must NOT be present (user sees own receipts only)
            $this->assertArrayNotHasKey('userName', $receipt);
        }
    }

    public function testListFiltersByStatus(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/my/receipts?status=pending', [], [], [
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
