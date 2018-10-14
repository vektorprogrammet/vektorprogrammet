<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class ProfileControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/profile');

        // Assert that we have the correct profile user
        $this->assertContains('Petter Johansen', $client->getResponse()->getContent());
        // Assert that we have the correct team and position
        $this->assertContains('Styret', $client->getResponse()->getContent());
        $this->assertContains('Leder', $client->getResponse()->getContent());

        // Check the count for Styret, Leder and Petter Johansen
        $this->assertEquals(1, $crawler->filter('html:contains("Styret")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Leder")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowSpecific()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/profile/4');

        // Assert that we have the correct profile user
        $this->assertContains('Thomas Alm', $client->getResponse()->getContent());
        $this->assertContains('alm@mail.com', $client->getResponse()->getContent());
        $this->assertContains('Brukeren er aktiv', $client->getResponse()->getContent());
        // Assert that we have the correct user level and department
        $this->assertContains('Teammedlem', $client->getResponse()->getContent());
        $this->assertContains('NTNU', $client->getResponse()->getContent());

        // Check the count for the different parameters
        $this->assertEquals(1, $crawler->filter('html:contains("NTNU")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Teammedlem")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("alm@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Brukeren er aktiv")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditProfileInformationAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/kontrollpanel/profil/rediger/4');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains(" Redigerer profil ")')->count());

        // Assert that we have the correct user
        $this->assertEquals(1, $crawler->filter('p:contains("Thomas Alm")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['editUser[firstName]'] = 'Thomas';
        $form['editUser[lastName]'] = 'Alm';
        $form['editUser[phone]'] = '99912399';
        $form['editUser[email]'] = 'alm@mail.com';
        $form['editUser[fieldOfStudy]']->select(2);

        // submit the form
        $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Follow the redirect
        $crawler = $client->followRedirect();

        $crawler = $this->goTo('/profile/4', $client);

        // Assert that we have the correct profile user
        $this->assertContains('Thomas Alm', $client->getResponse()->getContent());
        $this->assertContains('alm@mail.com', $client->getResponse()->getContent());
        $this->assertContains('Brukeren er aktiv', $client->getResponse()->getContent());
        // Assert that we have the correct user level, department, and field of study
        $this->assertContains('Teammedlem', $client->getResponse()->getContent());
        $this->assertContains('NTNU', $client->getResponse()->getContent());
        $this->assertContains('MIDT', $client->getResponse()->getContent());

        // Check the count for the different parameters
        $this->assertEquals(1, $crawler->filter('html:contains("NTNU")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("999 12 399")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("alm@mail.com")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Brukeren er aktiv")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditProfileInformation()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/profil/rediger');

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains(" Redigerer din profil ")')->count());

        $form = $crawler->selectButton('Lagre')->form();

        // Change the value of a field
        $form['editUser[firstName]'] = 'Petter';
        $form['editUser[lastName]'] = 'Johansen';
        $form['editUser[phone]'] = '22211133';
        $form['editUser[email]'] = 'petjo@mail.com';
        $form['editUser[fieldOfStudy]']->select(2);

        // submit the form
        $crawler = $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we have the correct profile user
        $this->assertContains('Petter Johansen', $client->getResponse()->getContent());
        $this->assertContains('petjo@mail.com', $client->getResponse()->getContent());
        // Assert that we have the correct user level, department, and field of study
        $this->assertContains('NTNU', $client->getResponse()->getContent());
        $this->assertContains('MIDT', $client->getResponse()->getContent());

        // Check the count for the different parameters
        $this->assertEquals(1, $crawler->filter('html:contains("MIDT")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("petjo@mail.com")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    //	public function testDownloadCertificateAction() {

//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//        $crawler = $client->request('GET', '/profile');

//		// Find the link called "Last ned attest"
//		$link = $crawler->filter('a:contains("Last ned attest")')->first()->link();

//		// Click the link
//		$crawler = $client->click($link);

//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );

//		// Assert that we have the correct certificate
//		$this->assertContains( 'Petter Johansen', $client->getResponse()->getContent() );
//		$this->assertContains( 'Attest', $client->getResponse()->getContent() );
//		$this->assertContains( 'Petter Johansen har ikke jobbet som assistent for vektorprogrammet. ', $client->getResponse()->getContent() );

//		// Assert that the "Content-Type" header is "application/pdf"
//		$this->assertTrue( $client->getResponse()->headers->contains(
//			'Content-Type',
//			'application/pdf'
//		));

//	}

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