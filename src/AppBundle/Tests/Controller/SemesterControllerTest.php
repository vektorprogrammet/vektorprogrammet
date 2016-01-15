<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SemesterControllerTest extends WebTestCase {
	
//	public function testShowSemestersByDepartment() {
//
//		// ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/semesteradmin/avdeling/1');
//
//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('h1:contains("Semester")')->count() );
//		$this->assertEquals( 1, $crawler->filter('td:contains("NTNU Vår 2015")')->count() );
//		$this->assertEquals( 1, $crawler->filter('td:contains("NTNU Vår 2014")')->count() );
//
//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
//
//		$crawler = $client->request('GET', '/semesteradmin/avdeling/2');
//
//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('h1:contains("Semester")')->count() );
//		$this->assertEquals( 1, $crawler->filter('td:contains("HiST Vår 2015")')->count() );
//
//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
//
//	}

//	public function testSuperadminCreateSemester() {
//
//		// ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/semesteradmin/avdeling/opprett/%7Bid%5D%7D?id=1');
//
//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('h1:contains("Opprett semester")')->count() );
//
//		$form = $crawler->selectButton('Opprett')->form();
//
//		// Change the value of a field
//		$form['createSemester[name]'] = 'Prove1';
//		$form['createSemester[semesterStartDate]'] = '2015-10-03 10:30:00 ';
//		$form['createSemester[semesterEndDate]'] = '2015-12-03 10:30:00 ';
//		$form['createSemester[admission_start_date]'] = '2015-12-04 10:30:00 ';
//		$form['createSemester[admission_end_date]'] = '2015-12-02 10:30:00 ';
//
//		// submit the form
//		$crawler = $client->submit($form);
//
//		// Assert a specific 302 status code
//		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );
//
//		// Assert that the response is the correct redirect
//		$this->assertTrue($client->getResponse()->isRedirect('/semesteradmin') );
//
//		// Follow the redirect
//		$crawler = $client->followRedirect();
//
//
//	}
	
//	public function testUpdateSemester(){
//
//		// ADMIN
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'petjo',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/semesteradmin/avdeling/1');
//
//		// Find a link and click it
//		$link = $crawler->selectLink('Rediger')->eq(2)->link();
//		$crawler = $client->click($link);
//
//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('h1:contains("Opprett semester")')->count() );
//
//		$form = $crawler->selectButton('Opprett')->form();
//
//		// Change the value of a field
//		$form['createSemester[name]'] = 'Prove2';
//		$form['createSemester[semesterStartDate]'] = '2016-10-03 10:30:00 ';
//		$form['createSemester[semesterEndDate]'] = '2016-12-03 10:30:00 ';
//		$form['createSemester[admission_start_date]'] = '2016-12-04 10:30:00 ';
//		$form['createSemester[admission_end_date]'] = '2016-12-02 10:30:00 ';
//
//		// submit the form
//		$crawler = $client->submit($form);
//
//		// Assert a specific 302 status code
//		$this->assertEquals( 302, $client->getResponse()->getStatusCode() );
//
//		// Assert that the response is the correct redirect
//		$this->assertTrue($client->getResponse()->isRedirect('/semesteradmin') );
//
//		// Follow the redirect
//		$crawler = $client->followRedirect();
//
//	}
	

    public function testShow() {
//
//        // TEAM
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'idaan',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/semesteradmin');
//
//		// Assert that we have the correct amount of data
//		$this->assertEquals( 1, $crawler->filter('h1:contains("Semester")')->count() );
//		$this->assertEquals( 1, $crawler->filter('td:contains("NTNU Vår 2015")')->count() );
//		$this->assertEquals( 1, $crawler->filter('td:contains("NTNU Vår 2014")')->count() );
//
//		// Assert a specific 200 status code
//		$this->assertEquals( 200, $client->getResponse()->getStatusCode() );
//
//		// USER
//		$client = static::createClient(array(), array(
//			'PHP_AUTH_USER' => 'thomas',
//			'PHP_AUTH_PW'   => '1234',
//		));
//
//		$crawler = $client->request('GET', '/skoleadmin/avdeling/2');
//
//		// Assert that the response is a redirect to /
//		$this->assertTrue( $client->getResponse()->isRedirect('/') );
//
    }
	
	/*
	Requires JQuery interaction, Symfony2 does not support that
	
	Phpunit was designed to test the PHP language, have to use another tool to test these. 
	
	public function testDeleteSemesterById() {}
	
	*/
}
