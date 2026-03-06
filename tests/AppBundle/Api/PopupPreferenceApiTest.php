<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PopupPreferenceApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testTogglePopupPreferenceRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/me/popup-preference', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testTogglePopupPreferenceReturns204(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/me/popup-preference', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(204);
    }
}
