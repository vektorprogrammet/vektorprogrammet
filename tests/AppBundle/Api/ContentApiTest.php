<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class ContentApiTest extends BaseWebTestCase
{
    private ?string $token = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getJwtToken();
    }

    private function getJwtToken(): string
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'username' => 'admin',
            'password' => '1234',
        ]));

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    // --- Article tests (preserved from ArticleApiTest) ---

    public function testGetArticleCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetArticleCollectionPublicAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/articles', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    // --- ChangeLogItem tests ---

    public function testGetChangeLogItemCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/change_log_items', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetChangeLogItemCollectionPublicAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/change_log_items', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    // --- Feedback tests ---

    public function testGetFeedbackCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/feedback', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetFeedbackCollectionRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/feedback', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    // --- Sponsor tests ---

    public function testGetSponsorCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/sponsors', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetSponsorCollectionPublicAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/sponsors', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    // --- Department tests ---

    public function testGetDepartmentCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/departments', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertNotEmpty($response);
        // Verify expected fields
        $dept = $response[0];
        $this->assertArrayHasKey('name', $dept);
        $this->assertArrayHasKey('city', $dept);
        $this->assertArrayHasKey('email', $dept);
        $this->assertArrayHasKey('latitude', $dept);
        $this->assertArrayHasKey('longitude', $dept);
    }

    public function testGetDepartmentItem(): void
    {
        $client = static::createClient();
        // First get collection to find an ID
        $client->request('GET', '/api/departments', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $departments = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($departments, 'Expected at least one department in fixture data');
        $id = $departments[0]['id'];

        $client->request('GET', "/api/departments/$id", [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseIsSuccessful();
        $dept = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $dept);
        $this->assertArrayHasKey('teams', $dept);
    }

    // --- AdmissionPeriod tests ---

    public function testGetAdmissionPeriodCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admission_periods', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    // --- InfoMeeting tests ---

    public function testGetInfoMeetingCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/info_meetings', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    // --- Team tests ---

    public function testGetTeamCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/teams', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetTeamItem(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/teams', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $teams = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($teams);
        $id = $teams[0]['id'];

        $client->request('GET', "/api/teams/$id", [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);
        $this->assertResponseIsSuccessful();
        $team = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('name', $team);
        $this->assertArrayHasKey('email', $team);
        $this->assertArrayHasKey('description', $team);
    }

    // --- TeamMembership tests ---

    public function testGetTeamMembershipCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/team_memberships', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    // --- FieldOfStudy tests ---

    public function testGetFieldOfStudyCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/field_of_studies', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    // --- Statistics tests ---

    public function testGetStatistics(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/statistics', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $stats = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('assistantCount', $stats);
        $this->assertArrayHasKey('teamMemberCount', $stats);
        $this->assertArrayHasKey('femaleAssistantCount', $stats);
        $this->assertArrayHasKey('maleAssistantCount', $stats);
        $this->assertIsInt($stats['assistantCount']);
        $this->assertIsInt($stats['teamMemberCount']);
    }

    // --- StaticContent tests ---

    public function testGetStaticContentCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/static_contents', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
    }

    public function testGetStaticContentCollectionPublicAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/static_contents', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
