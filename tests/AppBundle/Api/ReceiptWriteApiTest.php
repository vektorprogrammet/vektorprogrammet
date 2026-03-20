<?php

namespace Tests\AppBundle\Api;

use App\Operations\Infrastructure\Entity\Receipt;
use Tests\BaseWebTestCase;

class ReceiptWriteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // ==================== POST /api/receipts ====================

    public function testCreateReceiptRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['description' => 'Test receipt', 'sum' => 100]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateReceiptSucceeds(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Office supplies for team event',
            'sum' => 250.50,
        ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateReceiptWithCustomDate(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Supplies purchased last week',
            'sum' => 99.99,
            'receiptDate' => '2026-02-28',
        ]));

        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateReceiptRejectsMissingDescription(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'sum' => 100,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateReceiptRejectsMissingSum(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Missing sum',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateReceiptRejectsZeroSum(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Zero sum receipt',
            'sum' => 0,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateReceiptRejectsNegativeSum(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Negative sum receipt',
            'sum' => -50,
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    // ==================== PUT /api/receipts/{id} ====================

    public function testEditReceiptRequiresAuth(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');

        $client = static::createClient();
        $client->request('PUT', '/api/receipts/'.$receiptId, [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['description' => 'Updated', 'sum' => 200]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditReceiptForbiddenForNonOwner(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['description' => 'Hacked', 'sum' => 9999]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditReceiptSucceeds(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Updated description',
            'sum' => 300.00,
        ]));

        $this->assertResponseStatusCodeSame(200);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
    }

    // ==================== DELETE /api/receipts/{id} ====================

    public function testDeleteReceiptRequiresAuth(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');

        $client = static::createClient();
        $client->request('DELETE', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteReceiptForbiddenForNonOwner(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteReceiptSucceedsForOwner(): void
    {
        // First create a receipt we can safely delete
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Receipt to delete',
            'sum' => 50,
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $receiptId = $data['id'];

        // Now delete it
        $client->request('DELETE', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteReceiptSucceedsForTeamLeader(): void
    {
        // Get both tokens upfront to avoid static client replacement mid-test
        $assistantToken = $this->getJwtToken('assistent', '1234');
        $leaderToken = $this->getJwtToken('teamleader', '1234');

        // Create a receipt as assistant
        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$assistantToken,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Receipt for team leader to delete',
            'sum' => 75,
        ]));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $receiptId = $data['id'];

        // Now delete as team leader
        $client->request('DELETE', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$leaderToken,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    // ==================== PUT non-PENDING receipt ====================

    public function testEditNonPendingReceiptReturnsForbidden(): void
    {
        $receiptId = $this->createRefundedReceipt('assistent');
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['description' => 'Should fail', 'sum' => 100]));

        $this->assertResponseStatusCodeSame(403);
    }

    // ==================== DELETE non-PENDING receipt by owner ====================

    public function testDeleteNonPendingReceiptByOwnerReturnsForbidden(): void
    {
        $receiptId = $this->createRefundedReceipt('assistent');
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    // ==================== Date validation ====================

    public function testCreateReceiptRejectsInvalidDateFormat(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/receipts', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Bad date receipt',
            'sum' => 100,
            'receiptDate' => 'not-a-date',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testEditReceiptRejectsInvalidDateFormat(): void
    {
        $receiptId = $this->getFixtureReceiptId('assistent');
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/receipts/'.$receiptId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'description' => 'Updated',
            'sum' => 200,
            'receiptDate' => '31/12/2026',
        ]));

        $this->assertResponseStatusCodeSame(422);
    }

    // ==================== Helpers ====================

    private function getFixtureReceiptId(string $username): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['user_name' => $username]);
        $this->assertNotNull($user, "Fixture user '$username' not found");

        $receipt = $em->getRepository(Receipt::class)->findOneBy([
            'user' => $user,
            'status' => Receipt::STATUS_PENDING,
        ]);
        $this->assertNotNull($receipt, "No pending receipt found for user '$username'");

        return $receipt->getId();
    }

    /**
     * Creates a receipt owned by the given user with STATUS_REFUNDED.
     * Uses direct entity manipulation since there's no API endpoint for status changes.
     */
    private function createRefundedReceipt(string $username): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(\App\Entity\User::class)->findOneBy(['user_name' => $username]);
        $this->assertNotNull($user, "Fixture user '$username' not found");

        $receipt = new Receipt();
        $receipt->setUser($user);
        $receipt->setDescription('Refunded receipt for test');
        $receipt->setSum(100);
        $receipt->setReceiptDate(new \DateTime('2026-01-15'));
        $receipt->setStatus(Receipt::STATUS_REFUNDED);
        $receipt->setRefundDate(new \DateTime('2026-01-20'));
        $em->persist($receipt);
        $em->flush();

        return $receipt->getId();
    }
}
