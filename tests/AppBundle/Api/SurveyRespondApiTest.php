<?php

namespace Tests\AppBundle\Api;

use Tests\BaseWebTestCase;

class SurveyRespondApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    /**
     * Helper: get a DBAL connection from the kernel container.
     * Does NOT create a new client, so it is safe to call between requests.
     */
    private function getConnection(): \Doctrine\DBAL\Connection
    {
        return static::getContainer()->get('doctrine')->getConnection();
    }

    /**
     * Helper: find a survey ID by target audience from the test DB.
     */
    private function findSurveyIdByAudience(int $targetAudience): ?int
    {
        $result = $this->getConnection()->fetchAssociative(
            'SELECT id FROM survey WHERE target_audience = ? LIMIT 1',
            [$targetAudience]
        );

        return $result ? (int) $result['id'] : null;
    }

    /**
     * Helper: get question IDs for a survey.
     */
    private function findQuestionIdsForSurvey(int $surveyId): array
    {
        $rows = $this->getConnection()->fetchAllAssociative(
            'SELECT question_id FROM survey_surveys_questions WHERE survey_id = ?',
            [$surveyId]
        );

        return array_map(fn ($r) => (int) $r['question_id'], $rows);
    }

    /**
     * Helper: build answer payload for a survey's questions.
     */
    private function buildAnswers(int $surveyId, string $answerText = 'Test answer'): array
    {
        $questionIds = $this->findQuestionIdsForSurvey($surveyId);
        $answers = [];
        foreach ($questionIds as $qid) {
            $answers[] = ['questionId' => $qid, 'answer' => $answerText];
        }

        return $answers;
    }

    /**
     * Helper: count SurveyTaken records for a given survey ID.
     */
    private function countSurveyTakenRecords(int $surveyId): int
    {
        return (int) $this->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM survey_taken WHERE survey_id = ?',
            [$surveyId]
        );
    }

    /**
     * Helper: count SurveyTaken records for a given survey+user.
     */
    private function countSurveyTakenForUser(int $surveyId, string $username): int
    {
        return (int) $this->getConnection()->fetchOne(
            'SELECT COUNT(*) FROM survey_taken st
             JOIN user u ON st.user_id = u.id
             WHERE st.survey_id = ? AND u.user_name = ?',
            [$surveyId, $username]
        );
    }

    /**
     * Helper: get the school_id from the most recent SurveyTaken for a user+survey.
     */
    private function getSurveyTakenSchoolId(int $surveyId, string $username): ?int
    {
        $result = $this->getConnection()->fetchOne(
            'SELECT st.school_id FROM survey_taken st
             JOIN user u ON st.user_id = u.id
             WHERE st.survey_id = ? AND u.user_name = ?
             ORDER BY st.id DESC LIMIT 1',
            [$surveyId, $username]
        );

        return $result !== false ? (int) $result : null;
    }

    // ------------------------------------------------------------------
    // 404: Non-existent survey
    // ------------------------------------------------------------------

    public function testRespondToNonExistentSurveyReturns404(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/surveys/99999/respond', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => []]));

        $this->assertResponseStatusCodeSame(404);
    }

    // ------------------------------------------------------------------
    // SCHOOL_SURVEY (targetAudience=0): anonymous, no auth required
    // ------------------------------------------------------------------

    public function testSchoolSurveyAnonymousReturns204AndPersistsRecord(): void
    {
        $surveyId = $this->findSurveyIdByAudience(0);
        if ($surveyId === null) {
            $this->markTestSkipped('No SCHOOL_SURVEY fixture found');
        }

        $countBefore = $this->countSurveyTakenRecords($surveyId);
        $answers = $this->buildAnswers($surveyId, 'School answer');

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers]));

        $this->assertResponseStatusCodeSame(204);

        // Verify SurveyTaken record was persisted
        $countAfter = $this->countSurveyTakenRecords($surveyId);
        $this->assertSame($countBefore + 1, $countAfter, 'A new SurveyTaken record should be persisted');
    }

    // ------------------------------------------------------------------
    // TEAM_SURVEY (targetAudience=1): requires auth
    // ------------------------------------------------------------------

    public function testTeamSurveyWithoutAuthReturns401(): void
    {
        $surveyId = $this->findSurveyIdByAudience(1);
        if ($surveyId === null) {
            $this->markTestSkipped('No TEAM_SURVEY fixture found');
        }

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => []]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testTeamSurveyWithAuthReturns204AndPersistsRecord(): void
    {
        $surveyId = $this->findSurveyIdByAudience(1);
        if ($surveyId === null) {
            $this->markTestSkipped('No TEAM_SURVEY fixture found');
        }

        $answers = $this->buildAnswers($surveyId, 'Team answer');
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers]));

        $this->assertResponseStatusCodeSame(204);

        // Verify exactly 1 SurveyTaken for this user+survey
        $count = $this->countSurveyTakenForUser($surveyId, 'teammember');
        $this->assertSame(1, $count, 'Exactly one SurveyTaken record should exist for this user');
    }

    public function testTeamSurveyResubmissionReplacesOldResponse(): void
    {
        $surveyId = $this->findSurveyIdByAudience(1);
        if ($surveyId === null) {
            $this->markTestSkipped('No TEAM_SURVEY fixture found');
        }

        $answers1 = $this->buildAnswers($surveyId, 'First response');
        $answers2 = $this->buildAnswers($surveyId, 'Second response');
        $token = $this->getJwtToken('teammember', '1234');

        // First submission
        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers1]));
        $this->assertResponseStatusCodeSame(204);

        $countAfterFirst = $this->countSurveyTakenForUser($surveyId, 'teammember');
        $this->assertSame(1, $countAfterFirst, 'One record after first submission');

        // Second submission -- should replace, not duplicate
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers2]));
        $this->assertResponseStatusCodeSame(204);

        // Verify old response was deleted: still exactly 1 record
        $countAfterSecond = $this->countSurveyTakenForUser($surveyId, 'teammember');
        $this->assertSame(1, $countAfterSecond, 'Old response should be replaced, not duplicated');
    }

    // ------------------------------------------------------------------
    // ASSISTANT_SURVEY (targetAudience=2): requires auth
    // ------------------------------------------------------------------

    public function testAssistantSurveyWithoutAuthReturns401(): void
    {
        $surveyId = $this->findSurveyIdByAudience(2);
        if ($surveyId === null) {
            $this->markTestSkipped('No ASSISTANT_SURVEY fixture found');
        }

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => []]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testAssistantSurveyWithAuthReturns204AndSetsSchool(): void
    {
        $surveyId = $this->findSurveyIdByAudience(2);
        if ($surveyId === null) {
            $this->markTestSkipped('No ASSISTANT_SURVEY fixture found');
        }

        $answers = $this->buildAnswers($surveyId, 'Assistant answer');
        // 'assistent' user has AssistantHistory in fixtures
        $token = $this->getJwtToken('assistent', '1234');

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers]));

        $this->assertResponseStatusCodeSame(204);

        // Verify SurveyTaken exists and school was set from AssistantHistory
        $count = $this->countSurveyTakenForUser($surveyId, 'assistent');
        $this->assertSame(1, $count, 'Exactly one SurveyTaken record should exist');

        $schoolId = $this->getSurveyTakenSchoolId($surveyId, 'assistent');
        $this->assertNotNull($schoolId, 'School should be set from AssistantHistory');
    }

    public function testAssistantSurveyResubmissionReplacesOldResponse(): void
    {
        $surveyId = $this->findSurveyIdByAudience(2);
        if ($surveyId === null) {
            $this->markTestSkipped('No ASSISTANT_SURVEY fixture found');
        }

        $answers1 = $this->buildAnswers($surveyId, 'First assistant response');
        $answers2 = $this->buildAnswers($surveyId, 'Second assistant response');
        $token = $this->getJwtToken('assistent', '1234');

        // First submission
        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers1]));
        $this->assertResponseStatusCodeSame(204);

        $countAfterFirst = $this->countSurveyTakenForUser($surveyId, 'assistent');
        $this->assertSame(1, $countAfterFirst, 'One record after first submission');

        // Second submission -- should replace, not duplicate
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers2]));
        $this->assertResponseStatusCodeSame(204);

        // Verify old response was replaced: still exactly 1 record
        $countAfterSecond = $this->countSurveyTakenForUser($surveyId, 'assistent');
        $this->assertSame(1, $countAfterSecond, 'Old response should be replaced, not duplicated');
    }

    public function testAssistantSurveyWithoutHistoryReturnsBadRequest(): void
    {
        $surveyId = $this->findSurveyIdByAudience(2);
        if ($surveyId === null) {
            $this->markTestSkipped('No ASSISTANT_SURVEY fixture found');
        }

        $answers = $this->buildAnswers($surveyId, 'No history answer');
        // 'teammember' has no AssistantHistory in fixtures
        $token = $this->getJwtToken('teammember', '1234');

        $client = static::createClient();
        $client->request('POST', "/api/surveys/{$surveyId}/respond", [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['answers' => $answers]));

        $this->assertResponseStatusCodeSame(400);
    }
}
