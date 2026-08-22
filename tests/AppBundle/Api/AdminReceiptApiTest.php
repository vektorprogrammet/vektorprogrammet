<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminReceiptApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetReceiptDashboardRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetReceiptDashboardAllowedForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testGetReceiptDashboardReturnsReceiptRows(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertNotEmpty($data);
        foreach ($data as $receipt) {
            $this->assertArrayHasKey('id', $receipt);
            $this->assertArrayHasKey('visualId', $receipt);
            $this->assertArrayHasKey('userName', $receipt);
            $this->assertArrayHasKey('description', $receipt);
            $this->assertArrayHasKey('sum', $receipt);
            $this->assertArrayHasKey('receiptDate', $receipt);
            $this->assertArrayHasKey('submitDate', $receipt);
            $this->assertContains($receipt['status'], ['pending', 'refunded', 'rejected']);
        }
    }
}
