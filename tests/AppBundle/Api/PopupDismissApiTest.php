<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class PopupDismissApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testDismissPopupRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/me/popup-dismiss', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDismissPopupReturns204(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/me/popup-dismiss', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDismissPopupSuppressesSurveyPopup(): void
    {
        // Get token before creating the test client (JwtAuthTrait creates its own client)
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();

        // First, verify the popup endpoint returns survey data before dismissal
        $client->request('GET', '/api/surveys/popup', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(200);

        $dataBefore = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $dataBefore, 'Pre-condition: survey should be available before dismiss');
        $this->assertNotNull($dataBefore['id']);

        // Dismiss the popup
        $client->request('POST', '/api/me/popup-dismiss', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));
        $this->assertResponseStatusCodeSame(204);

        // Now verify the popup is suppressed (lastPopUpTime was just set to now,
        // so the "less than 1 day" guard should return empty data)
        $client->request('GET', '/api/surveys/popup', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseStatusCodeSame(200);

        $dataAfter = json_decode($client->getResponse()->getContent(), true);
        // Empty SurveyPopupResource serializes as [] because null props are omitted
        $this->assertEmpty($dataAfter, 'Survey popup should be suppressed after dismiss');
    }
}
