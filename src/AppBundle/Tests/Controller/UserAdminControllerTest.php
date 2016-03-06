<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAdminControllerTest extends WebTestCase {
	
	public function testCreateUser(){

		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/brukeradmin');

		// Find a link and click it
		$link = $crawler->selectLink('Opprett bruker')->eq(0)->link();
		$crawler = $client->click($link);


		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett bruker")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createUser[firstName]'] = "fornavn2";
		$form['createUser[lastName]'] = "etternavn2";
		$form['createUser[gender]']->select(0);
		$form['createUser[phone]'] = "22288222";
		$form['createUser[user_name]'] = "fornavn2";
		$form['createUser[Password]'] = "1234";
		$form['createUser[Email]'] = "fornavn2@mail.com";
		$form['createUser[fieldOfStudy]']->select(1);
		$form['createUser[role]']->select(0);


		// submit the form
		$crawler = $client->submit($form);

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/brukeradmin') );

		// Follow the redirect
		$crawler = $client->followRedirect();

		restoreDatabase();

	}

	public function testCreateUserSuperadmin() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/brukeradmin/opprett/2');

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett bruker")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createUser[firstName]'] = "fornavn1";
		$form['createUser[lastName]'] = "etternavn1";
		$form['createUser[gender]']->select(0);
		$form['createUser[phone]'] = "66688666";
		$form['createUser[user_name]'] = "fornavn1";
		$form['createUser[Password]'] = "1234";
		$form['createUser[Email]'] = "fornavn1@mail.com";
		$form['createUser[fieldOfStudy]']->select(3);
		$form['createUser[role]']->select(0);


		// submit the form
		$crawler = $client->submit($form);

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/brukeradmin') );

		// Follow the redirect
		$crawler = $client->followRedirect();

		restoreDatabase();
	}
	
	public function testShowUsersByDepartment() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/brukeradmin/avdeling/1');

		// Assert that we have the correct amount of data
		$this->assertEquals( 2, $crawler->filter('h1:contains("Brukere")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Reidun")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Siri")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Brenna Eskeland")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

	}
	
	public function testShow() {

		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/brukeradmin');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Brukere")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Reidun")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Siri")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Brenna Eskeland")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

	}
	
	/*
	Requires JQuery interaction, Symfony2 does not support that
	
	Phpunit was designed to test the PHP language, have to use another tool to test these. 
	
	public function testDeleteUserById() {}
	
	*/
	
}
