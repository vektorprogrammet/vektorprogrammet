<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase {

    public function testShow() {
		
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profile');
		
		// Assert that we have the correct profile user 
		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
		// Assert that we have the correct team and position 
		$this->assertContains( 'Hovedstyret', $client->getResponse()->getContent() );
		$this->assertContains( 'Leder', $client->getResponse()->getContent() );
		
		
		// Check the count for Hovedstyret, Leder and Petter Johansen
		$this->assertEquals( 1, $crawler->filter('html:contains("Hovedstyret")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("Leder")')->count() );
		
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		
    }
	
	public function testShowSpecific() {
		
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profile/4');
		
		// Assert that we have the correct profile user 
		$this->assertContains( 'Thomas Alm', $client->getResponse()->getContent() );
		$this->assertContains( 'alm@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'Brukeren er aktiv', $client->getResponse()->getContent() );
		// Assert that we have the correct user level and department
		$this->assertContains( 'Admin', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU', $client->getResponse()->getContent() );
		
		
		// Check the count for the different parameters 
		$this->assertEquals( 1, $crawler->filter('html:contains("NTNU")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("Admin")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("alm@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("Brukeren er aktiv")')->count() );
		
		// Assert that there are exactly 3 h2 tags on the page
		$this->assertCount(3, $crawler->filter('h2'));
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

		
    }
	
	public function testEditProfileInformationAdminAction() {
		
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profil/rediger/4');
		
		
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains(" Redigerer profil ")')->count());
		
		// Assert that we have the correct user 
		$this->assertEquals(1, $crawler->filter('p:contains("Thomas Alm")')->count());
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['editUserAdmin[firstName]'] = 'Thomas';
		$form['editUserAdmin[lastName]'] = 'Alm';
		$form['editUserAdmin[phone]'] = '99912399';
		$form['editUserAdmin[email]'] = 'alm@mail.com';
		$form['editUserAdmin[fieldOfStudy]']->select(2);
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		
		// Assert that we have the correct profile user 
		$this->assertContains( 'Thomas Alm', $client->getResponse()->getContent() );
		$this->assertContains( 'alm@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'Brukeren er aktiv', $client->getResponse()->getContent() );
		$this->assertContains( 'MIDT', $client->getResponse()->getContent() );
		// Assert that we have the correct user level, department, and field of study 
		$this->assertContains( 'Admin', $client->getResponse()->getContent() );
		$this->assertContains( 'NTNU', $client->getResponse()->getContent() );
		$this->assertContains( 'MIDT', $client->getResponse()->getContent() );
		
		// Check the count for the different parameters 
		$this->assertEquals( 1, $crawler->filter('html:contains("NTNU")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("Admin")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("alm@mail.com")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("Brukeren er aktiv")')->count() );
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		
	}
	
	/* Did not manage to get this one working since it was a repeated password field
	I couldn't figure out how to reach the fields in the code written below 
	
	public function testEditProfilePasswordAction() {
		
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profil/rediger/passord/');
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains(" Redigerer passord ")')->count());
		
		// Assert that we have the correct user 
		$this->assertEquals(1, $crawler->filter('p:contains("Petter Johansen")')->count());
		
		
		// Fill in the form and submit it
		$form = $crawler->selectButton('Lagre')->form(array(
			'editUserPassword[password][first_name]'  => '1234',            <--- Didn't work
			'editUserPassword[password]'  => '1234',                             <--- Didn't work either, so I didn't manage to insert any new data 
		));
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
	
	}
	*/
	
	public function testEditProfileInformationAction(){
	
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profil/rediger');
		
		
		
		// Assert that we have the correct page 
		$this->assertEquals(1, $crawler->filter('h1:contains(" Redigerer profil ")')->count());
		
		// Assert that we have the correct user 
		$this->assertEquals(1, $crawler->filter('p:contains("Petter Johansen")')->count());
		
		$form = $crawler->selectButton('Lagre')->form();
		
		// Change the value of a field
		$form['editUser[firstName]'] = 'Petter';
		$form['editUser[lastName]'] = 'Johansen';
		$form['editUser[phone]'] = '22211133';
		$form['editUser[email]'] = 'petjo@mail.com';
		$form['editUser[fieldOfStudy]']->select(3);
		
		// submit the form
		$crawler = $client->submit($form);
		
		// Assert a specific 302 status code
		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );
		
		// Follow the redirect 
		$crawler = $client->followRedirect();
		
		
		// Assert that we have the correct profile user 
		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'petjo@mail.com', $client->getResponse()->getContent() );
		$this->assertContains( 'BITA', $client->getResponse()->getContent() );
		// Assert that we have the correct user level, department, and field of study 
		$this->assertContains( 'HiST', $client->getResponse()->getContent() );
		$this->assertContains( 'BITA', $client->getResponse()->getContent() );
		
		// Check the count for the different parameters 
		$this->assertEquals( 1, $crawler->filter('html:contains("HiST")')->count() );
		$this->assertEquals( 1, $crawler->filter('html:contains("petjo@mail.com")')->count() );
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		
	}
	
	public function testDownloadCertificateAction() {
	
		$client = static::createClient(array(), array(
			'PHP_AUTH_USER' => 'petjo',
			'PHP_AUTH_PW'   => '1234',
		));
        $crawler = $client->request('GET', '/profile');
		
		// Find the link called "Last ned attest"
		$link = $crawler->filter('a:contains("Last ned attest")')->first()->link();
		
		// Click the link
		$crawler = $client->click($link);
		
		// Assert a specific 200 status code
		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
		
		// Assert that we have the correct certificate 
		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
		$this->assertContains( 'Attest', $client->getResponse()->getContent() );
		$this->assertContains( 'Petter Johansen har ikke jobbet som assistent for vektorprogrammet. ', $client->getResponse()->getContent() );
		
		// Assert that the "Content-Type" header is "application/pdf"
		$this->assertTrue( $client->getResponse()->headers->contains(
			'Content-Type',
			'application/pdf'
		));
		
	}
	
	/*
	Requires JQuery interaction, Symfony2 does not support that
	
	Phpunit was designed to test the PHP language, have to use another tool to test these. 
	
	public function testPromoteToAdminAction() {}
	public function testPromoteToAssistentAction() {}
	public function testPromoteToTeamMemberAction() {}
	public function testActivateUserAction() {}
	public function testDeactivateUserAction() {}
	
	*/
	
	/*
	The people that made the methods below must write their own functional tests 
	Missing:
	
	public function testEditProfilePhotoAction() {}
	public function testShowEditProfilePhotoAction() {}
	
	*/
	
}





























