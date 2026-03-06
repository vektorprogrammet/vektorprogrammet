<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class AdminSurveyApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetSurveysRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/surveys', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetSurveysReturnsSuccessfulResponse(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('surveys', $data);
        $this->assertIsArray($data['surveys']);
    }

    public function testGetSurveysReturnsSurveyFields(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/surveys', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        // Fixtures create surveys in dep-1 + semester-current, which is the default
        // for the teammember user
        $this->assertNotEmpty($data['surveys']);

        $survey = $data['surveys'][0];
        $this->assertArrayHasKey('id', $survey);
        $this->assertArrayHasKey('name', $survey);
        $this->assertArrayHasKey('targetAudience', $survey);
        $this->assertArrayHasKey('confidential', $survey);
        $this->assertArrayHasKey('totalAnswered', $survey);
        $this->assertIsInt($survey['totalAnswered']);
    }

    public function testGetSurveysWithNonExistentDepartmentReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/surveys?department=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('surveys', $data);
        $this->assertIsArray($data['surveys']);
        $this->assertEmpty($data['surveys']);
    }

    public function testGetSurveysWithNonExistentSemesterReturnsEmpty(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/surveys?semester=99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('surveys', $data);
        $this->assertIsArray($data['surveys']);
        $this->assertEmpty($data['surveys']);
    }
}
