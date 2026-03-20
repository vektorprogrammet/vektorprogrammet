<?php

namespace Tests\AppBundle\Api;

use App\Operations\Infrastructure\Entity\AssistantHistory;
use Tests\BaseWebTestCase;

class AdminCertificateApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testCertificateRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/certificates/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCertificateRequiresTeamLeaderRole(): void
    {
        $ahId = $this->getAssistantHistoryId();
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/certificates/'.$ahId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCertificateReturnsDataForTeamLeader(): void
    {
        $ahId = $this->getAssistantHistoryId();
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/certificates/'.$ahId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('userName', $data);
        $this->assertArrayHasKey('schoolName', $data);
        $this->assertArrayHasKey('semesterName', $data);
        $this->assertArrayHasKey('departmentName', $data);
        $this->assertArrayHasKey('workdays', $data);
        $this->assertArrayHasKey('bolk', $data);
        $this->assertArrayHasKey('day', $data);
    }

    public function testCertificateNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/certificates/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    private function getAssistantHistoryId(): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $ah = $em->getRepository(AssistantHistory::class)->findOneBy([]);
        $this->assertNotNull($ah, 'Fixture should have at least one assistant history');

        return $ah->getId();
    }
}
