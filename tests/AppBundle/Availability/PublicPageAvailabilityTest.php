<?php

namespace Tests\App\Availability;

use Tests\BaseWebTestCase;

class PublicPageAvailabilityTest extends BaseWebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider(): array
    {
        return [
            ['/'],
            ['/assistenter'],
            ['/team'],
            ['/laerere'],
            ['/omvektor'],
            ['/kontakt'],

            ['/nyheter'],
            ['/nyheter/ntnu'],
            ['/nyhet/1'],

            ['/profile/1'],

            ['/opptak'],
            ['/opptak/NTNU'],
            ['/opptak/avdeling/1'],
            ['/opptak/Bergen'],
            ['/opptak/ås'],

            ['/avdeling/Trondheim'],
            ['/avdeling/NTNU'],
            ['/avdeling/ås'],
        ];
    }
}
