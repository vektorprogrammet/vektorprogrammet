<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShow()
    {

//		// Admin
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));

//        $crawler = $client->request('GET', '/forum/21/subforum/tråd/1');

//		// Assert that we have the correct  data
//		$this->assertContains( 'Tråd 1', $client->getResponse()->getContent() );
//		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
//		$this->assertContains( 'Post 1', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('a:contains("Hovedstyret")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("Tråd 1")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("Tråd 1")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("Post 1")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("Post 1")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//		// ********************** //

//		// User
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));

//        $crawler = $client->request('GET', '/forum/22/subforum/tråd/5');

//		// Assert that we have the correct  data
//		$this->assertContains( 'hue hue', $client->getResponse()->getContent() );
//		$this->assertContains( 'Gimse', $client->getResponse()->getContent() );
//		$this->assertContains( 'asdasd', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('a:contains("Gimse")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("hue hue")')->count() );
//		$this->assertEquals( 2, $crawler->filter('p:contains("asdasda")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("asdasd")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("asdasdaa")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//		// ********************** //

//		// Team
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));

//		$crawler = $client->request('GET', '/forum');

//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);

//		// Find a link and click it
//		$link = $crawler->selectLink('Hovedstyret')->eq(0)->link();
//		$crawler = $client->click($link);

//		// Find a link and click it
//		$link = $crawler->selectLink('Tråd 2')->eq(0)->link();
//		$crawler = $client->click($link);

//        // Assert that we have the correct  data
//		$this->assertContains( 'Tråd 2', $client->getResponse()->getContent() );
//		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
//		$this->assertContains( 'lol', $client->getResponse()->getContent() );

//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('a:contains("Hovedstyret")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("Tråd 2")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("Tråd 2")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("lol")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("rofl")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
    }

//	public function testEditPost() {

//		// Team
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));

//		$crawler = $client->request('GET', '/forum');

//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);

//		// Find a link and click it
//		$link = $crawler->selectLink('Hovedstyret')->eq(0)->link();
//		$crawler = $client->click($link);

//		// Find a link and click it
//		$link = $crawler->selectLink('Tråd 2')->eq(0)->link();
//		$crawler = $client->click($link);

//		// Find a link and click it
//		$link = $crawler->selectLink('Rediger')->eq(1)->link();
//		$crawler = $client->click($link);

//		$form = $crawler->selectButton('Lagre')->form();

//		// Change the value of a field
//		$form['createPost[Subject]'] = 'Heggen';
//		$form['createPost[text]'] = 'Heggen er best!';

//		// submit the form
//		$crawler = $client->submit($form);

//		// Assert a specific 302 status code
//		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

//		// Follow the redirect
//		$crawler = $client->followRedirect();

//		// Assert that we created a new entity
//		$this->assertContains( 'Heggen', $client->getResponse()->getContent() );
//		$this->assertContains( 'Heggen er best!', $client->getResponse()->getContent() );
//		$this->assertContains( 'Tråd 2', $client->getResponse()->getContent() );

//		// Check the count for the different variables
//		$this->assertEquals( 1, $crawler->filter('h3:contains("Heggen")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("Heggen er best!")')->count() );
//		$this->assertEquals( 1, $crawler->filter('h3:contains("Tråd 2")')->count() );
//		$this->assertEquals( 1, $crawler->filter('p:contains("Tråd 2")')->count() );

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//	}

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeletePost() {}

    */
}
