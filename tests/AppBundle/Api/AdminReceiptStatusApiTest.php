<?php

namespace Tests\AppBundle\Api;

use App\Entity\Receipt;
use Tests\BaseWebTestCase;

class AdminReceiptStatusApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testReceiptStatusRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/1/status', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'refunded']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testReceiptStatusForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/1/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'refunded']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testReceiptStatusForbiddenForTeamMember(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/1/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'refunded']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSetReceiptStatusRefunded(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $receiptId = $this->getPendingReceiptId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/'.$receiptId.'/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'refunded']));

        $this->assertResponseStatusCodeSame(204);

        // Verify refundDate was populated in DB
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();
        $receipt = $em->getRepository(Receipt::class)->find($receiptId);
        $this->assertNotNull($receipt->getRefundDate(), 'refundDate should be set after refund');
    }

    public function testSetReceiptStatusRejected(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $receiptId = $this->getPendingReceiptId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/'.$receiptId.'/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'rejected']));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testSetReceiptStatusPending(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $receiptId = $this->getPendingReceiptId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/'.$receiptId.'/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'pending']));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testSetReceiptStatusInvalidStatus(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $receiptId = $this->getPendingReceiptId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/'.$receiptId.'/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'invalid_status']));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testSetReceiptStatusBlankStatus(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $receiptId = $this->getPendingReceiptId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/'.$receiptId.'/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => '']));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testSetReceiptStatusNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/receipts/99999/status', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['status' => 'refunded']));

        $this->assertResponseStatusCodeSame(404);
    }

    private function getPendingReceiptId(): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $receipt = $em->getRepository(Receipt::class)
            ->findOneBy(['status' => 'pending']);

        $this->assertNotNull($receipt, 'No pending receipt found in fixtures');

        return $receipt->getId();
    }
}
