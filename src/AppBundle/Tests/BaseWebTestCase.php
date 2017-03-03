<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseWebTestCase extends WebTestCase
{
    private static $anonymousClient;
    private static $assistantClient;
    private static $teamMemberClient;
    private static $teamLeaderClient;
    private static $adminClient;

    protected static function createAnonymousClient() : Client
    {
        if (self::$anonymousClient === null) {
            self::$anonymousClient = self::createClient();
        }

        return self::$anonymousClient;
    }

    protected static function createAssistantClient() : Client
    {
        if (self::$assistantClient === null) {
            self::$assistantClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'assistant',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return self::$assistantClient;
    }

    protected static function createTeamMemberClient() : Client
    {
        if (self::$teamMemberClient === null) {
            self::$teamMemberClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'team',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return self::$teamMemberClient;
    }

    protected static function createTeamLeaderClient() : Client
    {
        if (self::$teamLeaderClient === null) {
            self::$teamLeaderClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return self::$teamLeaderClient;
    }

    protected static function createAdminClient() : Client
    {
        if (self::$adminClient === null) {
            self::$adminClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'superadmin',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return self::$adminClient;
    }

    protected function goTo(Client $client, string $path) : Crawler
    {
        $crawler = $client->request('GET', $path);

        $this->assertTrue($client->getResponse()->isSuccessful());

        return $crawler;
    }

    protected function anonymousGoTo(string $path) : Crawler
    {
        return $this->goTo(self::createAnonymousClient(), $path);
    }

    protected function assistantGoTo(string $path) : Crawler
    {
        return $this->goTo(self::createAssistantClient(), $path);
    }

    protected function teamMemberGoTo(string $path) : Crawler
    {
        return $this->goTo(self::createTeamMemberClient(), $path);
    }

    protected function teamLeaderGoTo(string $path) : Crawler
    {
        return $this->goTo(self::createTeamLeaderClient(), $path);
    }

    protected function adminGoTo(string $path) : Crawler
    {
        return $this->goTo(self::createAdminClient(), $path);
    }

    protected function countTableRows(string $path, Client $client = null) : int
    {
        if ($client === null) {
            $client = self::createAdminClient();
        }

        $crawler = $this->goTo($client, $path);

        return $crawler->filter('tr')->count();
    }
}
