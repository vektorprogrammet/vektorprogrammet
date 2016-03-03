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
            array('/artikkel/1'),
            array('/artikkel/2'),
            array('/artikkel/3'),
            array('/artikkel/4'),
            array('/artikkel/5'),
            array('/artikkel/6'),
            array('/artikkel/7'),
            array('/artikkel/8'),
        );
    }

    /**
     * @dataProvider userUrlProvider
     * @param $url
     */
    public function testUserPageIsSuccessful($url)
    {
        $client = $this->createLoggedInClient('ROLE_USER');
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function userUrlProvider()
    {
        return array(
            array('/medlemmer'),
//            array('/profile'),
        );
    }

    private function createLoggedInClient($role)
    {
        $client = self::createClient();
        $session = $client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('user', null, $firewall, array($role));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
        return $client;
    }
}