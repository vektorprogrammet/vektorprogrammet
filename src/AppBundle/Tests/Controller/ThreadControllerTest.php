<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThreadControllerTest extends WebTestCase {
	
	public function testCreateThread() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/21/subforum/1');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Opprett tråd')->eq(0)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subject1';
		$form['createThread[text]'] = 'text1';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subject1")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("text1")')->count() );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/21/subforum/1');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Opprett tråd')->eq(0)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subjectHEHE';
		$form['createThread[text]'] = 'textHEHE';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subjectHEHE")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("textHEHE")')->count() );
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/23/subforum/5');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Opprett tråd')->eq(0)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subjectUSER1';
		$form['createThread[text]'] = 'textUSER1';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subjectUSER1")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("textUSER1")')->count() );
		
	}
	
	public function testEditThread() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/21/subforum/1');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(3)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subject2';
		$form['createThread[text]'] = 'text2';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subject2")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("text2")')->count() );
		$this->assertEquals( 0, $crawler->filter('h3:contains("subject1")')->count() );
		$this->assertEquals( 0, $crawler->filter('p:contains("text1")')->count() );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/21/subforum/1');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(1)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subjectHAHA';
		$form['createThread[text]'] = 'textHAHA';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subjectHAHA")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("textHAHA")')->count() );
		$this->assertEquals( 0, $crawler->filter('h3:contains("subjectHEHE")')->count() );
		$this->assertEquals( 0, $crawler->filter('p:contains("textHEHE")')->count() );
			
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum/23/subforum/5');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(0)->link();
		$crawler = $client->click($link);
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createThread[subject]'] = 'subjectUSER2';
		$form['createThread[text]'] = 'textUSER2';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h3:contains("subjectUSER2")')->count() );
		$this->assertEquals( 1, $crawler->filter('p:contains("textUSER2")')->count() );
		$this->assertEquals( 0, $crawler->filter('h3:contains("subjectUSER1")')->count() );
		$this->assertEquals( 0, $crawler->filter('p:contains("textUSER1")')->count() );	
		
	}
	
    public function testShowSpecificSubforumThreads() {
	
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Team')->eq(1)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
		
		// Find a link and click it 
		$link = $crawler->selectLink('Hovedstyret')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
		$this->assertEquals(1, $crawler->filter('forum:contains("Tråd 1")')->count());
		$this->assertEquals(1, $crawler->filter('forum:contains("Tråd 2")')->count());
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Team')->eq(1)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
		
		// Find a link and click it 
		$link = $crawler->selectLink('Hovedstyret')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
		$this->assertEquals(1, $crawler->filter('forum:contains("Tråd 1")')->count());
		$this->assertEquals(1, $crawler->filter('forum:contains("Tråd 2")')->count());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/forum');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Generelt')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Generelt")')->count());
		
		// Find a link and click it 
		$link = $crawler->selectLink('Spørsmål til vektor')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Spørsmål til vektor")')->count());
	  
    }
	
	
}
