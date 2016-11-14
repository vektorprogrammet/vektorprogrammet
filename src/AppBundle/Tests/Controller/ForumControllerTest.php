<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumControllerTest extends WebTestCase
{
    public function testShow()
    {

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));

//        $crawler = $client->request('GET', '/forum');

//		// Assert that we have the correct  data
//		$this->assertContains( 'Forum', $client->getResponse()->getContent() );
//		$this->assertContains( 'Team', $client->getResponse()->getContent() );
//		$this->assertContains( 'Forum for teams.', $client->getResponse()->getContent() );
//		$this->assertContains( 'Skole', $client->getResponse()->getContent() );
//		$this->assertContains( 'Forum for skoler.', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Forum for teams.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Team")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Skole")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Forum for skoler.")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));

//		$crawler = $client->request('GET', '/forum');

//		// Assert that we have the correct  data
//		$this->assertContains( 'Forum', $client->getResponse()->getContent() );
//		$this->assertContains( 'Generelt', $client->getResponse()->getContent() );
//		$this->assertContains( 'Skole', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 0, $crawler->filter('forum:contains("Forum for teams.")')->count() );
//		$this->assertEquals( 0, $crawler->filter('forum:contains("Team")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Skole")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Forum for skoler.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Generelt for alle.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Generelt")')->count() );

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));

//		$crawler = $client->request('GET', '/forum');

//		// Assert that we have the correct data
//		$this->assertContains( 'Forum', $client->getResponse()->getContent() );
//		$this->assertContains( 'Generelt', $client->getResponse()->getContent() );
//		$this->assertContains( 'Skole', $client->getResponse()->getContent() );
//		$this->assertContains( 'Team', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Forum for teams.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Team")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Skole")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Forum for skoler.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Generelt for alle.")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Generelt")')->count() );
    }

//	public function testEditForum() {

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//        $crawler = $client->request('GET', '/forum');

//		// Find a link and click it
//		$link = $crawler->selectLink('Rediger')->link();
//		$crawler = $client->click($link);

//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett forum")')->count());

//		$form = $crawler->selectButton('Lagre')->form();

//		// Change the value of a field
//		$form['createForum[name]'] = 'Prove';
//		$form['createForum[description]'] = 'Et prove forum';
//		$form['createForum[type]']->select("general");

//		// submit the form
//		$crawler = $client->submit($form);

//		// Assert a specific 302 status code
//		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

//		// Assert that the response is the correct redirect
//		$this->assertTrue($client->getResponse()->isRedirect('/forum') );

//		// Follow the redirect
//		$crawler = $client->followRedirect();

//		// Assert that we created a new entity
//		$this->assertContains( 'Prove', $client->getResponse()->getContent() );
//		$this->assertContains( 'Et prove forum', $client->getResponse()->getContent() );

//		// Check the count for the different variables
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Prove")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Et prove forum")')->count() );

//		$this->assertEquals( 0, $crawler->filter('forum:contains("Team")')->count() );
//		$this->assertEquals( 0, $crawler->filter('forum:contains("Forum for teams.")')->count() );

//		$this->assertEquals( 3, $crawler->filter('a:contains("Rediger")')->count() );
//		$this->assertEquals( 4, $crawler->filter('a:contains("Slett")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//	}

//	public function testCreateForum() {

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//        $crawler = $client->request('GET', '/forum');

//		// Find a link and click it
//		$link = $crawler->selectLink('Opprett forum')->link();
//		$crawler = $client->click($link);

//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett forum")')->count());

//		$form = $crawler->selectButton('Lagre')->form();

//		// Change the value of a field
//		$form['createForum[name]'] = 'Prove2';
//		$form['createForum[description]'] = 'Et prove forum2';
//		$form['createForum[type]']->select("general");

//		// submit the form
//		$crawler = $client->submit($form);

//		// Assert a specific 302 status code
//		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

//		// Assert that the response is the correct redirect
//		$this->assertTrue($client->getResponse()->isRedirect('/forum') );

//		// Follow the redirect
//		$crawler = $client->followRedirect();

//		// Assert that we created a new entity
//		$this->assertContains( 'Prove2', $client->getResponse()->getContent() );
//		$this->assertContains( 'Et prove forum2', $client->getResponse()->getContent() );

//		// Check the count for the different variables
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Prove2")')->count() );
//		$this->assertEquals( 1, $crawler->filter('forum:contains("Et prove forum2")')->count() );

//		$this->assertEquals( 4, $crawler->filter('a:contains("Rediger")')->count() );
//		$this->assertEquals( 5, $crawler->filter('a:contains("Slett")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//	}

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteForum() {}

    */
}
