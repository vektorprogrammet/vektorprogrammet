<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamAdminControllerTest extends WebTestCase {
    
	public function testCreatePosition(){
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Stillinger')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
		
		// Find a link and click it 
		$link = $crawler->selectLink('Opprett stilling')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett stilling")')->count());
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createPosition[name]'] = 'Nestleder1';

		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Nestleder1")')->count());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/opprett/stilling');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/opprett/stilling');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		$crawler = $client->request('GET', '/teamadmin/opprett/stilling');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
	}
	
	public function testEditPosition() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Stillinger')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(1)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett stilling")')->count());
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['createPosition[name]'] = 'Nestleder2';

		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Nestleder2")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("Nestleder1")')->count());
		
	}
	
	
	public function testRemovePosition() {
		
		// NOTE:
		// Cannot test this function fully as Symfony2.6 does not support JQuery interaction
		
		//ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/stilling/slett/10');
		
		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());
		
		//TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/stilling/slett/10');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/stilling/slett/10');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
	}
	
	public function testShowPositions() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Stillinger')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Stillinger")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Leder")')->count());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/opprett/stilling');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/stillinger');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		$crawler = $client->request('GET', '/teamadmin/stillinger');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
	}

	public function testCreateTeamForDepartment() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/opprett/2');
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett team")')->count());
		
		$form = $crawler->selectButton('Opprett')->form();
		
		// Change the value of a field
		$form['createTeam[name]'] = 'testteam1';

		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Tea")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Pølseteamet")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("testteam1")')->count());

		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/opprett/2');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/opprett/2');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/opprett/1');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
	}
	
	public function testUpdateTeam() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(1)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett team")')->count());
		
		$form = $crawler->selectButton('Opprett')->form();
		
		// Change the value of a field
		$form['createTeam[name]'] = 'testteam2';

		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Tea")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Pølseteamet")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("testteam2")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("testteam1")')->count());
		
	}
	
	public function testDeleteTeamById() {
		
		// NOTE:
		// Cannot test this function fully as Symfony2.6 does not support JQuery interaction
		
		//ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/slett/13');
		
		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());
		
		//TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/slett/13');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/slett/13');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
		
		
	}
	

	
	public function testAddUserToTeam() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/3');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Legg til bruker')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett arbeidshistorie")')->count());
		
		$form = $crawler->selectButton('Opprett')->form();
		
		// Change the value of a field
		$form['createWorkHistory[user]']->select(1);
		$form['createWorkHistory[position]']->select(5);
		$form['createWorkHistory[startDate]'] = '2015-08-03 10:30:00';
		$form['createWorkHistory[endDate]'] = '2015-12-04 11:00:00';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Pølseteamet")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("2015-08-03")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("2015-12-04")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Petter")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("Johansen")')->count());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/leggTilBruker/3');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/leggTilBruker/3');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
	}
	
	public function testUpdateWorkHistory() {
	
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/3');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Rediger')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Opprett arbeidshistorie")')->count());
		
		$form = $crawler->selectButton('Opprett')->form();
		
		// Change the value of a field
		$form['createWorkHistory[user]']->select(65);
		$form['createWorkHistory[position]']->select(5);
		$form['createWorkHistory[startDate]'] = '2016-08-03 10:30:00';
		$form['createWorkHistory[endDate]'] = '2016-12-04 11:00:00';
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Pølseteamet")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("2015-08-03")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("2015-12-04")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("Petter")')->count());
		$this->assertEquals(0, $crawler->filter('td:contains("Johansen")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("2016-08-03")')->count());
		$this->assertEquals(1, $crawler->filter('td:contains("2016-12-04")')->count());
	}
	
	public function testRemoveUserFromTeamById(){
		
		// NOTE:
		// Cannot test this function fully as Symfony2.6 does not support JQuery interaction
		
		
		//ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/slett/bruker/10');
		
		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());
		
		//TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/slett/bruker/10');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/slett/bruker/10');
		
		// Assert that the response content contains a string
		$this->assertContains('Du har ikke tilstrekkelige rettigheter.', $client->getResponse()->getContent());
		
	}
	
	public function testShowTeamsByDepartment() {
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/1');
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Team")')->count());
		
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('a:contains("Hovedstyret")')->count() );
        $this->assertEquals( 1, $crawler->filter('a:contains("Opptakstyret")')->count() );
			
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/1');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/avdeling/1');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
	}
	
	
	public function  testShowSpecificTeam() { 
		
		// ADMIN
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Pølseteamet')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Pølseteamet")')->count());
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Find a link and click it 
		$link = $crawler->selectLink('Hovedstyret')->eq(0)->link();
		$crawler = $client->click($link);
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains("Hovedstyret")')->count());
		
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('td:contains("Petter")')->count() );
        $this->assertEquals( 1, $crawler->filter('td:contains("Johansen")')->count() );
		$this->assertEquals( 1, $crawler->filter('td:contains("Andreassen")')->count() );
        $this->assertEquals( 1, $crawler->filter('td:contains("Ida")')->count() );
		
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/1');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin/team/3');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
	}
	
	public function testShow() {
		
		// TEAM
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'idaan',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Check the count for the different variables 
		$this->assertEquals( 1, $crawler->filter('h1:contains("Team")')->count() );
		$this->assertEquals( 1, $crawler->filter('a:contains("Hovedstyret")')->count() );
        $this->assertEquals( 1, $crawler->filter('a:contains("Opptakstyret")')->count() );
		
		// USER
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'thomas',
			'PHP_AUTH_PW'   => '1234',
		));
		
		$crawler = $client->request('GET', '/teamadmin');
		
		// Assert that the response is a redirect
		$this->assertTrue( $client->getResponse()->isRedirect('/') );
		
    }
	
}
