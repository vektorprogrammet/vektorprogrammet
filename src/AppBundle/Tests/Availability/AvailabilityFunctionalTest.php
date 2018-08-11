<?php

namespace AppBundle\Tests\Availability;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider publicUrlProvider
     *
     * @param $url
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider userUrlProvider
     *
     * @param $url
     */
    public function testUserPageIsSuccessful($url)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider userUrlProvider
     *
     * @param $url
     */
    public function testUserPageIsDenied($url)
    {
        //Check if anonymous users gets denied
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider adminUrlProvider
     *
     * @param $url
     */
    public function testAdminPageIsSuccessful($url)
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider adminUrlProvider
     *
     * @param $url
     */
    public function testAdminPageIsDenied($url)
    {
        //Check if anonymous users gets denied
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertFalse($client->getResponse()->isSuccessful());

        //Check if assistants gets denied
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));
        $client->request('GET', $url);

        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    public function publicUrlProvider()
    {
        return array(
            array('/'),
            array('/assistenter'),
            array('/team'),
            array('/laerere'),
            array('/omvektor'),
            array('/kontakt'),
            array('/nyheter'),
            array('/nyheter/ntnu'),
            array('/nyhet/1'),
            array('/profile/1'),
        );
    }

    public function userUrlProvider()
    {
        return array(
            array('/profile'),
        );
    }

    public function adminUrlProvider()
    {
        return array(
            array('/kontrollpanel/brukeradmin/opprett'),
            array('/kontrollpanel/opptak'),
            array('/kontrollpanel/opprettsoker'),
            array('/kontrollpanel/brukeradmin'),
            array('/kontrollpanel/semesteradmin'),
            array('/kontrollpanel/statistikk/opptak/1'),
            array('/kontrollpanel/elfinder'),
            array('/kontrollpanel/artikkeladmin'),
            array('/kontrollpanel/vikar'),
            array('/kontrollpanel/intervju/skjema'),
            array('/kontrollpanel'),
            array('/kontrollpanel/undersokelse/admin'),
            array('/kontrollpanel/undersokelse/opprett'),
        );
    }
}
