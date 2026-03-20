<?php

namespace Tests\AppBundle\Api;

use App\Survey\Infrastructure\Entity\Survey;
use App\Survey\Infrastructure\Entity\SurveyNotificationCollection;
use App\Entity\UserGroup;
use App\Entity\UserGroupCollection;
use Doctrine\ORM\EntityManagerInterface;
use Tests\BaseWebTestCase;

class AdminSurveyNotifierApiTest extends BaseWebTestCase
{
    use JwtAuthTrait;

    private function createUserGroupCollection(EntityManagerInterface $em): UserGroupCollection
    {
        $collection = new UserGroupCollection();
        $collection->setName('Test UG Collection');
        $collection->setNumberUserGroups(1);
        $em->persist($collection);

        $userGroup = new UserGroup();
        $userGroup->setName('Test User Group');
        $userGroup->setUserGroupCollection($collection);
        $em->persist($userGroup);

        $em->flush();

        return $collection;
    }

    private function createNotificationCollection(EntityManagerInterface $em, bool $active = false, bool $allSent = false, ?\DateTime $timeOfNotification = null): SurveyNotificationCollection
    {
        $survey = $em->getRepository(Survey::class)->findAll()[0];
        $ugCollection = $this->createUserGroupCollection($em);
        $userGroups = $em->getRepository(UserGroup::class)->findBy(['userGroupCollection' => $ugCollection]);

        $notifCollection = new SurveyNotificationCollection();
        $notifCollection->setName('Test Notifier');
        $notifCollection->setSurvey($survey);
        $notifCollection->setTimeOfNotification($timeOfNotification ?? new \DateTime('tomorrow'));
        $notifCollection->setActive($active);
        $notifCollection->setAllSent($allSent);
        $notifCollection->setUserGroups($userGroups);
        $em->persist($notifCollection);
        $em->flush();

        return $notifCollection;
    }

    // ===== POST /api/admin/survey-notifiers (create) =====

    public function testCreateRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Test']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $survey = $em->getRepository(Survey::class)->findAll()[0];
        $ugCollection = $this->createUserGroupCollection($em);
        $userGroups = $em->getRepository(UserGroup::class)->findBy(['userGroupCollection' => $ugCollection]);
        $userGroupIds = array_map(fn (UserGroup $ug) => $ug->getId(), $userGroups);

        $payload = [
            'name' => 'New Notifier',
            'surveyId' => $survey->getId(),
            'timeOfNotification' => '2030-06-15T10:00:00+00:00',
            'notificationType' => 0,
            'userGroupIds' => $userGroupIds,
        ];

        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(201);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
    }

    public function testCreateValidationRejectsBlankName(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $survey = $em->getRepository(Survey::class)->findAll()[0];

        $payload = [
            'name' => '',
            'surveyId' => $survey->getId(),
            'timeOfNotification' => '2030-06-15T10:00:00+00:00',
        ];

        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateRejectsInvalidSurveyId(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Test Notifier',
            'surveyId' => 99999,
            'timeOfNotification' => '2030-06-15T10:00:00+00:00',
        ];

        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testCreateRejectsMissingSurveyId(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $payload = [
            'name' => 'Test Notifier',
            'timeOfNotification' => '2030-06-15T10:00:00+00:00',
        ];

        $client->request('POST', '/api/admin/survey-notifiers', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(422);
    }

    // ===== PUT /api/admin/survey-notifiers/{id} (edit) =====

    public function testEditRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/admin/survey-notifiers/1', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();
        $client->request('PUT', '/api/admin/survey-notifiers/1', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testEditSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em);

        $payload = [
            'name' => 'Updated Notifier Name',
            'emailSubject' => 'New Subject',
        ];

        $client->request('PUT', '/api/admin/survey-notifiers/'.$notifCollection->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertSame($notifCollection->getId(), $data['id']);
    }

    public function testEditNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('PUT', '/api/admin/survey-notifiers/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode(['name' => 'Updated']));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testEditRejectsActiveCollection(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em, active: true);

        $payload = [
            'name' => 'Should Not Update',
        ];

        $client->request('PUT', '/api/admin/survey-notifiers/'.$notifCollection->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode($payload));

        $this->assertResponseStatusCodeSame(409);
    }

    // ===== DELETE /api/admin/survey-notifiers/{id} =====

    public function testDeleteRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/admin/survey-notifiers/1', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em);

        $client->request('DELETE', '/api/admin/survey-notifiers/'.$notifCollection->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testDeleteSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em);
        $id = $notifCollection->getId();

        $client->request('DELETE', '/api/admin/survey-notifiers/'.$id, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(204);

        // Verify deletion
        $em->clear();
        $deleted = $em->getRepository(SurveyNotificationCollection::class)->find($id);
        $this->assertNull($deleted);
    }

    public function testDeleteNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('DELETE', '/api/admin/survey-notifiers/99999', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testDeleteRejectsActiveCollection(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em, active: true);

        $client->request('DELETE', '/api/admin/survey-notifiers/'.$notifCollection->getId(), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(409);
    }

    // ===== POST /api/admin/survey-notifiers/{id}/send =====

    public function testSendRequiresAuth(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/admin/survey-notifiers/1/send', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSendForbiddenForAssistant(): void
    {
        $token = $this->getJwtToken('assistent', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em);

        $client->request('POST', '/api/admin/survey-notifiers/'.$notifCollection->getId().'/send', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSendSuccess(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        // Create a collection with timeOfNotification in the past and not all sent
        $notifCollection = $this->createNotificationCollection($em, active: false, allSent: false, timeOfNotification: new \DateTime('-1 hour'));

        $client->request('POST', '/api/admin/survey-notifiers/'.$notifCollection->getId().'/send', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(200);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $data);
    }

    public function testSendNotFoundReturns404(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $client->request('POST', '/api/admin/survey-notifiers/99999/send', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testSendRejectsWhenTimeOfNotificationInFuture(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em, active: false, allSent: false, timeOfNotification: new \DateTime('+1 day'));

        $client->request('POST', '/api/admin/survey-notifiers/'.$notifCollection->getId().'/send', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(409);
    }

    public function testSendRejectsWhenAllSent(): void
    {
        $token = $this->getJwtToken('teammember', '1234');
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $notifCollection = $this->createNotificationCollection($em, active: false, allSent: true, timeOfNotification: new \DateTime('-1 hour'));

        $client->request('POST', '/api/admin/survey-notifiers/'.$notifCollection->getId().'/send', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ], json_encode([]));

        $this->assertResponseStatusCodeSame(409);
    }
}
