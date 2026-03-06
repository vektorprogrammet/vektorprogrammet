<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PartnersApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetPartnersRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/me/partners', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetPartnersReturnsArray(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/me/partners', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('partners', $data);
        $this->assertIsArray($data['partners']);
    }
}
