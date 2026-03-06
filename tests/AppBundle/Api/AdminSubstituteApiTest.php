<?php

namespace Tests\AppBundle\Api;

use App\Entity\Application;
use Tests\BaseWebTestCase;

class AdminSubstituteApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    // --- List endpoint ---

    public function testListRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/substitutes', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testListRequiresTeamMemberRole(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/substitutes', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testListReturnsSubstitutesForCurrentSemester(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();
        $client->request('GET', '/api/admin/substitutes', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);

        // Fixtures have substitutes in admission-period-current (dep-1):
        // application0 (substitute=true), application21 (substitute=true)
        $this->assertNotEmpty($data, 'Should have substitute data from fixtures');

        $first = $data[0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('email', $first);
        // yearOfStudy and language may be null (omitted by API Platform)
        // monday-friday are booleans and always present
        $this->assertArrayHasKey('monday', $first);
        $this->assertArrayHasKey('tuesday', $first);
        $this->assertArrayHasKey('wednesday', $first);
        $this->assertArrayHasKey('thursday', $first);
        $this->assertArrayHasKey('friday', $first);
    }

    // --- Edit endpoint ---

    public function testEditRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/substitutes/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditRequiresTeamLeaderRole(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        // Get a substitute application ID
        $substituteId = $this->getSubstituteApplicationId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/substitutes/'.$substituteId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'monday' => true,
            'tuesday' => false,
            'wednesday' => true,
            'thursday' => false,
            'friday' => true,
            'yearOfStudy' => 2,
            'language' => 'Norsk',
        ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditNonSubstituteReturnsBadRequest(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        // Get a non-substitute application ID
        $nonSubId = $this->getNonSubstituteApplicationId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/substitutes/'.$nonSubId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'monday' => true,
            'tuesday' => false,
            'wednesday' => true,
            'thursday' => false,
            'friday' => true,
            'yearOfStudy' => 2,
            'language' => 'Norsk',
        ]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testEditSubstituteSuccess(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');
        $substituteId = $this->getSubstituteApplicationId();

        $client = static::createClient();
        $client->request('PUT', '/api/admin/substitutes/'.$substituteId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'monday' => true,
            'tuesday' => false,
            'wednesday' => true,
            'thursday' => false,
            'friday' => true,
            'yearOfStudy' => 2,
            'language' => 'Norsk',
        ]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($substituteId, $data['id']);
    }

    public function testEditNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/substitutes/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([
            'monday' => true,
            'tuesday' => false,
            'wednesday' => true,
            'thursday' => false,
            'friday' => true,
            'yearOfStudy' => 2,
            'language' => 'Norsk',
        ]));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Activate endpoint ---

    public function testActivateRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/1/activate', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testActivateRequiresTeamLeaderRole(): void
    {
        $nonSubId = $this->getNonSubstituteApplicationId();
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$nonSubId.'/activate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testActivateAlreadySubstituteReturnsBadRequest(): void
    {
        $substituteId = $this->getSubstituteApplicationId();
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$substituteId.'/activate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testActivateSuccess(): void
    {
        $nonSubId = $this->getNonSubstituteApplicationId();
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$nonSubId.'/activate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($nonSubId, $data['id']);
    }

    public function testActivateNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/99999/activate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Deactivate endpoint ---

    public function testDeactivateRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/1/deactivate', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeactivateRequiresTeamLeaderRole(): void
    {
        $substituteId = $this->getSubstituteApplicationId();
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$substituteId.'/deactivate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeactivateSuccess(): void
    {
        $substituteId = $this->getSubstituteApplicationId();
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$substituteId.'/deactivate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseIsSuccessful();

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals($substituteId, $data['id']);
    }

    public function testDeactivateNonSubstituteReturnsBadRequest(): void
    {
        $nonSubId = $this->getNonSubstituteApplicationId();
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/'.$nonSubId.'/deactivate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testDeactivateNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('POST', '/api/admin/substitutes/99999/deactivate', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }

    // --- Helpers ---

    private function getSubstituteApplicationId(): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $app = $em->getRepository(Application::class)->findOneBy(['substitute' => true]);
        $this->assertNotNull($app, 'Fixture should have at least one substitute application');

        return $app->getId();
    }

    private function getNonSubstituteApplicationId(): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $app = $em->getRepository(Application::class)->findOneBy(['substitute' => false]);
        $this->assertNotNull($app, 'Fixture should have at least one non-substitute application');

        return $app->getId();
    }
}
