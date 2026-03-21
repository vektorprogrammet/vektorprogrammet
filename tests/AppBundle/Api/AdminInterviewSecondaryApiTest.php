<?php

namespace Tests\AppBundle\Api;

use App\Admission\Infrastructure\Entity\Application;
use App\Interview\Infrastructure\Entity\Interview;
use App\Identity\Infrastructure\Entity\User;
use Tests\BaseWebTestCase;

class AdminInterviewSecondaryApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testCoInterviewerRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/1/co-interviewer', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testClearCoInterviewerRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/1/co-interviewer', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteInterviewRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCoInterviewerForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/1/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testClearCoInterviewerForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/1/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteInterviewForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testAssignCoInterviewerSelfAssign(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        // Find an interview ID from fixtures — interview5 is the one with responseCode='code'
        $interviewId = $this->getInterviewIdByResponseCode('code');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interviewId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAssignCoInterviewerNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/99999/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAssignCoInterviewerWithUserId(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        // Find a valid user to assign as co-interviewer
        $em = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $this->assertNotNull($user);

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interviewId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => $user->getId()]));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAssignCoInterviewerUserNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interviewId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['userId' => 99999]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSelfAssignWhenInterviewerReturns400(): void
    {
        // Interview5 (responseCode='code') has interviewer=user-2 (idaan).
        // Logging in as idaan and self-assigning should be rejected.
        $token = $this->getJwtToken('idaan', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$interviewId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testSelfAssignOnConductedInterviewReturns400(): void
    {
        // Find a conducted interview (interviewed=true)
        $em = static::getContainer()->get('doctrine')->getManager();
        $conductedInterview = $em->getRepository(Interview::class)
            ->findOneBy(['interviewed' => true]);
        $this->assertNotNull($conductedInterview, 'No conducted interview found in fixtures');
        $conductedId = $conductedInterview->getId();

        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('PUT', '/api/admin/interviews/'.$conductedId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(400);
    }

    public function testClearCoInterviewer(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/'.$interviewId.'/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testClearCoInterviewerNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/99999/co-interviewer', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteInterview(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/'.$interviewId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteInterviewNotFound(): void
    {
        $token = $this->getJwtToken('teamleader', '1234');

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteInterviewCascadeNullsApplication(): void
    {
        $token = $this->getJwtToken('admin', '1234');

        $interviewId = $this->getInterviewIdByResponseCode('code');

        // Get the application ID associated with this interview
        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)->find($interviewId);
        $this->assertNotNull($interview);
        $application = $interview->getApplication();
        $this->assertNotNull($application);
        $applicationId = $application->getId();

        $client = static::createClient();
        $client->request('DELETE', '/api/admin/interviews/'.$interviewId, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify the application's interview was nulled
        $em->clear();
        $application = $em->getRepository(Application::class)->find($applicationId);
        $this->assertNotNull($application, 'Application should still exist');
        $this->assertNull($application->getInterview(), 'Interview should be null after deletion');
    }

    private function getInterviewIdByResponseCode(string $responseCode): int
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $interview = $em->getRepository(Interview::class)
            ->findByResponseCode($responseCode);

        $this->assertNotNull($interview, 'Interview with response code "'.$responseCode.'" not found in fixtures');

        return $interview->getId();
    }
}
