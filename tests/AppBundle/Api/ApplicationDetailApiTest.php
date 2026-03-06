<?php

namespace Tests\AppBundle\Api;

use App\Entity\Application;
use Tests\BaseWebTestCase;

class ApplicationDetailApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetApplicationRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/applications/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetApplicationReturnsData(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        // Find a valid application ID from the database
        $client = static::createClient();
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $application = $em->getRepository(Application::class)->findOneBy([]);
        $this->assertNotNull($application, 'Fixtures should contain at least one application');
        $applicationId = $application->getId();

        $client->request('GET', '/api/admin/applications/'.$applicationId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('userName', $data);
        $this->assertArrayHasKey('userEmail', $data);
        $this->assertArrayHasKey('previousParticipation', $data);
        $this->assertArrayHasKey('yearOfStudy', $data);
        $this->assertArrayHasKey('monday', $data);
        $this->assertArrayHasKey('tuesday', $data);
        $this->assertArrayHasKey('wednesday', $data);
        $this->assertArrayHasKey('thursday', $data);
        $this->assertArrayHasKey('friday', $data);
        $this->assertArrayHasKey('created', $data);
        $this->assertEquals($applicationId, $data['id']);
    }

    public function testGetApplicationNotFound(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/applications/999999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
