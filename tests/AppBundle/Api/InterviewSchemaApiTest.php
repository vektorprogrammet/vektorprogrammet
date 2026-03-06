<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class InterviewSchemaApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetInterviewSchemasRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/interview-schemas', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetInterviewSchemasReturnsArray(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/interview-schemas', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $first = $data[0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('questionCount', $first);
        $this->assertIsInt($first['questionCount']);
    }
}
