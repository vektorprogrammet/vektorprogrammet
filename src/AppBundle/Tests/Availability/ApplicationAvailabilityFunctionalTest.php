<?php

namespace AppBundle\Tests\Availability;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider publicUrlProvider
     * @param $url
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function publicUrlProvider()
    {
        return array(
            array('/'),
            array('/omvektor'),
            array('/styretogteam'),
            array('/studenter'),
            array('/skoler'),
            array('/bedrifter'),
            array('/artikkel'),
            array('/artikkel/ntnu'),
            array('/artikkel/hist'),
            array('/artikkel/nmbu'),
            array('/artikkel/uio'),
            array('/artikkel/uib'),
            array('/artikkel/10'),
            array('/artikkel/11'),
            array('/artikkel/12'),
            array('/artikkel/13'),
            array('/artikkel/14'),
            array('/artikkel/15'),
            array('/artikkel/16'),
            array('/artikkel/17'),
            array('/artikkel/18'),
            array('/artikkel/19'),
            array('/artikkel/20'),
            array('/artikkel/21'),
        );
    }

    /**
     * @dataProvider userUrlProvider
     * @param $url
     */
    public function testUserPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client = $this->logIn($client);
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function userUrlProvider()
    {
        return array(
            array('/medlemmer'),
            array('/profile'),
        );
    }

    private function logIn($client)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('user', null, $firewall, array('ROLE_USER'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $client;
    }
}