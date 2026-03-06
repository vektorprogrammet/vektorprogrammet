<?php

namespace Tests\App\Availability;

use Tests\BaseWebTestCase;

class AssistantPageAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = $this->createAssistantClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsDeniedForAnonymous(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function urlProvider(): array
    {
        return [
            ['/profile'],
            ['/profil/rediger/passord/'],
            ['/min-side'],
            ['/utlegg'],
        ];
    }
}
