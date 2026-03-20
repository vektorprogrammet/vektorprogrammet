<?php

namespace Tests\AppBundle\Api;

use App\Entity\Department;
use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyTaken;
use App\Entity\User;
use Tests\BaseWebTestCase;

class SurveyResultApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    public function testGetSurveyResultRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetSurveyResultReturns404ForInvalidId(): void
    {
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetSurveyResultReturnsSurveyData(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $survey = $em->getRepository(Survey::class)->findOneBy([]);
        $this->assertNotNull($survey, 'Fixture should contain at least one survey');
        $id = $survey->getId();

        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('survey', $data);
        $this->assertArrayHasKey('answers', $data);
    }

    public function testConfidentialSurveyDeniedForNonAdmin(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Find a survey in teammember's department (dep-1) and make it confidential
        $survey = $em->getRepository(Survey::class)->findOneBy(['confidential' => false]);
        $this->assertNotNull($survey, 'Fixture should contain a non-confidential survey');
        $survey->setConfidential(true);
        $em->flush();
        $id = $survey->getId();

        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testConfidentialSurveyAllowedForAdmin(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Use a confidential survey (set by previous test or set one now)
        $survey = $em->getRepository(Survey::class)->findOneBy(['confidential' => true]);
        if ($survey === null) {
            $survey = $em->getRepository(Survey::class)->findOneBy([]);
            $survey->setConfidential(true);
            $em->flush();
        }
        $id = $survey->getId();

        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testCrossDepartmentSurveyDeniedForNonAdmin(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Find a survey and assign it to a different department than teammember's (dep-1)
        $otherDept = $em->getRepository(Department::class)->createQueryBuilder('d')
            ->where('d.id != :deptId')
            ->setParameter('deptId', 1)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
        $this->assertNotNull($otherDept, 'Fixture should contain a department other than dep-1');

        $survey = $em->getRepository(Survey::class)->findOneBy(['confidential' => false]);
        if ($survey === null) {
            // All surveys made confidential by previous test; reset one
            $survey = $em->getRepository(Survey::class)->findOneBy([]);
            $survey->setConfidential(false);
        }
        $survey->setDepartment($otherDept);
        $em->flush();
        $id = $survey->getId();

        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCrossDepartmentSurveyAllowedForAdmin(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Find a non-confidential survey in a different department
        $otherDept = $em->getRepository(Department::class)->createQueryBuilder('d')
            ->where('d.id != :deptId')
            ->setParameter('deptId', 1)
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $survey = $em->getRepository(Survey::class)->findOneBy([]);
        $survey->setConfidential(false);
        $survey->setDepartment($otherDept);
        $em->flush();
        $id = $survey->getId();

        $token = $this->getJwtToken('admin', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testSurveyResultIncludesSerializedAnswers(): void
    {
        $kernel = self::bootKernel();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Find a non-confidential team survey in teammember's department
        $user = $em->getRepository(User::class)->findOneBy(['user_name' => 'teammember']);
        $this->assertNotNull($user);
        $dept = $user->getDepartment();

        $survey = $em->getRepository(Survey::class)->findOneBy([
            'department' => $dept,
            'confidential' => false,
            'targetAudience' => Survey::$TEAM_SURVEY,
        ]);

        if ($survey === null) {
            // Previous tests may have modified surveys; find any and reset it
            $survey = $em->getRepository(Survey::class)->findOneBy([
                'targetAudience' => Survey::$TEAM_SURVEY,
            ]);
            $this->assertNotNull($survey, 'Fixture should contain a team survey');
            $survey->setDepartment($dept);
            $survey->setConfidential(false);
            $em->flush();
        }

        // Create a SurveyTaken record so the answers array_map is exercised
        $surveyTaken = new SurveyTaken();
        $surveyTaken->setSurvey($survey);
        $surveyTaken->setUser($user);
        $em->persist($surveyTaken);
        $em->flush();

        $id = $survey->getId();

        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('GET', '/api/admin/survey-results/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('answers', $data);
        $this->assertNotEmpty($data['answers'], 'Answers should contain serialized SurveyTaken data');
        $this->assertIsArray($data['answers'][0], 'Each answer should be a serialized array');
    }
}
