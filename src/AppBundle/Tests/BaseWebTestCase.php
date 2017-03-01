<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class BaseWebTestCase extends WebTestCase
{
    private $anonymousClient;
    private $assistantClient;
    private $teamMemberClient;
    private $teamLeaderClient;
    private $adminClient;

    protected function createAnonymousClient() : Client
    {
        if ($this->anonymousClient === null) {
            $this->anonymousClient = self::createClient();
        }

        return $this->anonymousClient;
    }

    protected function createAssistantClient() : Client
    {
        if ($this->assistantClient === null) {
            $this->assistantClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'assistant',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return $this->assistantClient;
    }

    protected function createTeamMemberClient() : Client
    {
        if ($this->teamMemberClient === null) {
            $this->teamMemberClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'team',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return $this->teamMemberClient;
    }

    protected function createTeamLeaderClient() : Client
    {
        if ($this->teamLeaderClient === null) {
            $this->teamLeaderClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return $this->teamLeaderClient;
    }

    protected function createAdminClient() : Client
    {
        if ($this->adminClient === null) {
            $this->adminClient = self::createClient(array(), array(
                'PHP_AUTH_USER' => 'superadmin',
                'PHP_AUTH_PW' => '1234',
            ));
        }

        return $this->adminClient;
    }

    protected function goTo(Client $client, string $path) : Crawler
    {
        $crawler = $client->request('GET', $path);

        $this->assertTrue($client->getResponse()->isSuccessful());

        return $crawler;
    }

    protected function anonymousGoTo(string $path) : Crawler
    {
        return $this->goTo($this->createAnonymousClient(), $path);
    }

    protected function assistantGoTo(string $path) : Crawler
    {
        return $this->goTo($this->createAssistantClient(), $path);
    }

    protected function teamMemberGoTo(string $path) : Crawler
    {
        return $this->goTo($this->createTeamMemberClient(), $path);
    }

    protected function teamLeaderGoTo(string $path) : Crawler
    {
        return $this->goTo($this->createTeamLeaderClient(), $path);
    }

    protected function adminGoTo(string $path) : Crawler
    {
        return $this->goTo($this->createAdminClient(), $path);
    }

    protected function countTableRows(string $path, Client $client = null) : int
    {
        if ($client === null) {
            $client = $this->createAdminClient();
        }

        $crawler = $this->goTo($client, $path);

        return $crawler->filter('tr')->count();
    }
}
