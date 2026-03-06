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

    public function testGetReceiptDashboardForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testGetReceiptDashboardReturnsAggregations(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('pendingCount', $data);
        $this->assertArrayHasKey('pendingTotalAmount', $data);
        $this->assertArrayHasKey('refundedCount', $data);
        $this->assertArrayHasKey('totalPayoutThisYear', $data);
        $this->assertArrayHasKey('avgRefundTimeHours', $data);
        $this->assertArrayHasKey('rejectedCount', $data);

        $this->assertIsInt($data['pendingCount']);
        $this->assertIsInt($data['refundedCount']);
        $this->assertIsInt($data['rejectedCount']);
        $this->assertIsInt($data['avgRefundTimeHours']);
    }
}
