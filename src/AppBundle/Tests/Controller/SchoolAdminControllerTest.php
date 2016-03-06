<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchoolAdminControllerTest extends WebTestCase {

	public function testCreateSchoolForDepartment() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/skoleadmin/opprett/1');

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett skole")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createSchool[name]'] = 'test1';
		$form['createSchool[contactPerson]'] = 'person1';
		$form['createSchool[phone]'] = '12312312';
		$form['createSchool[email]'] = 'test1@mail.com';

		// submit the form
		$client->submit($form);

		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/skoleadmin') );

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/opprett/1');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/opprett/1');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

        restoreDatabase();

	}

	public function testUpdateSchool() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/skoleadmin/avdeling/1');

		// Find a link and click it
		$link = $crawler->selectLink('Rediger')->eq(1)->link();
		$crawler = $client->click($link);

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett skole")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createSchool[name]'] = 'test2';
		$form['createSchool[contactPerson]'] = 'person2';
		$form['createSchool[phone]'] = '99912399';
		$form['createSchool[email]'] = 'test2@mail.com';

		// submit the form
		$client->submit($form);

		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/skoleadmin') );

		restoreDatabase();

	}


	public function testShowSchoolsByDepartment() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin/avdeling/1');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler NTNU")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Gimse")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per Olsen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per@mail.com")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		$crawler = $client->request('GET', '/skoleadmin/avdeling/2');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler HiST")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Blussuvoll")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Kari Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("kari@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Internasjonal")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/avdeling/2');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/avdeling/2');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

		restoreDatabase();

	}


	public function testShowUsersByDepartment() {

		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'team',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin/brukere');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Tildel skole")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Brenna Eskeland")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Ravnå")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Myrvoll-Nilsen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Rasdal Håland")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/brukere');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

	}


	public function testShowUsersByDepartmentSuperadmin() {

		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin/avdeling/2');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler HiST")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Blussuvoll")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Kari Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("kari@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Internasjonal")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		$crawler = $client->request('GET', '/skoleadmin/avdeling/1');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler NTNU")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Gimse")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per Olsen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per@mail.com")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/avdeling/2');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );


	}


	public function testDelegateSchoolToUser() {

		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin/brukere/avdeling/1');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Tildel skole")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Reidun")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count() );

		// Find a link and click it
		$link = $crawler->selectLink('Tildel skole')->eq(1)->link();
		$crawler = $client->click($link);

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett assistent historie")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createAssistantHistory[Semester]']->select(1);
		$form['createAssistantHistory[workdays]']->select("4");
		$form['createAssistantHistory[School]']->select(2);
		$form['createAssistantHistory[bolk]']->select("Bolk 2");
		$form['createAssistantHistory[day]']->select("Onsdag");

		// submit the form
		$client->submit($form);

		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/skoleadmin/brukere/avdeling/1') );

		// Follow the redirect
		$client->followRedirect();

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'admin',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/skoleadmin/brukere/avdeling/1');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Tildel skole")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Reidun")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Persdatter Ødegaard")')->count() );

		// Find a link and click it
		$link = $crawler->selectLink('Tildel skole')->eq(1)->link();
		$crawler = $client->click($link);

		// Assert that we have the correct page
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett assistent historie")')->count());

		$form = $crawler->selectButton('Opprett')->form();

		// Change the value of a field
		$form['createAssistantHistory[Semester]']->select(1);
		$form['createAssistantHistory[workdays]']->select("4");
		$form['createAssistantHistory[School]']->select(2);
		$form['createAssistantHistory[bolk]']->select("Bolk 2");
		$form['createAssistantHistory[day]']->select("Onsdag");

		// submit the form
		$client->submit($form);

		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );

		// Assert that the response is the correct redirect
		$this->assertTrue($client->getResponse()->isRedirect('/skoleadmin/brukere/avdeling/1') );

		// Follow the redirect
		$client->followRedirect();

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );


		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin/brukere/avdeling/1');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

		restoreDatabase();

	}


	public function  testShowSpecificSchool() {

		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin');

		// Find a link and click it
		$link = $crawler->selectLink('Selsbakk')->link();
		$crawler = $client->click($link);

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Selsbakk")')->count() );
		$this->assertEquals( 1, $crawler->filter('h3:contains("Inaktive personer")')->count() );
		$this->assertEquals( 1, $crawler->filter('h3:contains("Aktive personer")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		$client->request('GET', '/skole/skole/2');

		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());

		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'team',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/skoleadmin');

		// Find a link and click it
		$link = $crawler->selectLink('Gimse')->link();
		$crawler = $client->click($link);

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Gimse")')->count() );
		$this->assertEquals( 1, $crawler->filter('h3:contains("Inaktive personer")')->count() );
		$this->assertEquals( 1, $crawler->filter('h3:contains("Aktive personer")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

	}




	public function testShow() {

		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));

        $crawler = $client->request('GET', '/skoleadmin');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Selsbakk")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Vibeke Hansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Vibeke@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("22386722")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'team',
			'PHP_AUTH_PW'   => '1234',
		));

		$crawler = $client->request('GET', '/skoleadmin');

		// Assert that we have the correct amount of data
		$this->assertEquals( 1, $crawler->filter('h1:contains("Skoler")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Gimse")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per Olsen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Per@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("99887722")')->count() );

		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'assistent',
			'PHP_AUTH_PW'   => '1234',
		));

		$client->request('GET', '/skoleadmin');

		// Assert that the response is a redirect to /
		$this->assertTrue( $client->getResponse()->isRedirect('/') );

    }

	/*
	Requires JQuery interaction, Symfony2 does not support that

	Phpunit was designed to test the PHP language, have to use another tool to test these.

	public function testDeleteSchoolById() {}
	public function testRemoveUserFromSchoolById() {]

	*/


}
