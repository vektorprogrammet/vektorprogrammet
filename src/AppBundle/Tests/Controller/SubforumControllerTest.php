<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubforumControllerTest extends WebTestCase {
    
//	public function testShow() {
//
//		//ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Skole')->eq(1)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Skole")')->count());
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('forum:contains("Gimse")')->count());
//		$this->assertEquals(1, $crawler->filter('forum:contains("Selsbakk")')->count());
//
//		// TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('forum:contains("Hovedstyret")')->count());
//		$this->assertEquals(0, $crawler->filter('forum:contains("Opptaksstyret")')->count());
//
//		// TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Generelt')->eq(0)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Generelt")')->count());
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('forum:contains("Spørsmål til vektor")')->count());
//
//
//		$crawler = $client->request('GET', '/forum/21');
//
//		// Assert that the response is a redirect
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
//	}
	
//	public function testCreateSubforumAction() {
//
//		//ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Opprett subforum')->eq(0)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett subforum")')->count());
//
//		$form = $crawler->selectButton('Lagre')->form();
//
//		// Change the value of a field
//		$form['createSubforum[name]'] = 'subforum1';
//		$form['createSubforum[teams]'][0]->tick();
//		$form['createSubforum[type]']->select("team");
//		$form['createSubforum[schoolDocument]'] = "<h1> school document1 </h1>";
//
//		// submit the form
//		$crawler = $client->submit($form);
//
//		// Follow the redirect
//		$crawler = $client->followRedirect();
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);
//
//		$this->assertEquals(1, $crawler->filter('forum:contains("subforum1")')->count());
//
//
//		// TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/opprett');
//
//		// Assert that the response is a redirect
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
//
//		// USER
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/opprett');
//
//		// Assert that the response is a redirect
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
//	}
	
//	public function testEditSubforum(){
//
//		//ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Rediger')->eq(3)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct page
//		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett subforum")')->count());
//
//		$form = $crawler->selectButton('Lagre')->form();
//
//		// Change the value of a field
//		$form['createSubforum[name]'] = 'subforum2';
//		$form['createSubforum[teams]'][1]->tick();
//		$form['createSubforum[type]']->select("team");
//		$form['createSubforum[schoolDocument]'] = "<h1> school document2 </h1>";
//
//		// submit the form
//		$crawler = $client->submit($form);
//
//		// Follow the redirect
//		$crawler = $client->followRedirect();
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Team')->eq(1)->link();
//		$crawler = $client->click($link);
//
//		$this->assertEquals(1, $crawler->filter('forum:contains("subforum2")')->count());
//		$this->assertEquals(0, $crawler->filter('forum:contains("subforum1")')->count());
//
//		// TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/rediger/20');
//
//		// Assert that the response is a redirect
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
//		// USER
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/rediger/20');
//
//		// Assert that the response is a redirect
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
//	}
	
	public function testDeleteSubforum() {
//
//		/*
//		NOTE:
//		Cannot test this function fully as Symfony2.6 does not support JQuery interaction
//		*/
//
//		//ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/slett/10');
//
//		// Assert that the response status code is 2xx
//		$this->assertTrue($client->getResponse()->isSuccessful());
//
//		//TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/slett/10');
//
//		// Assert that the response content contains a string
//		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
//
//		// USER
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/forum/subforum/slett/10');
//
//		// Assert that the response content contains a string
//		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
//
//
	}

}
