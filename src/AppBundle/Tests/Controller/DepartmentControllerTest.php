<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class DepartmentControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
        $crawler = $this->adminGoTo('/kontrollpanel/avdelingadmin');

        // Assert that we have the correct department data
        $this->assertEquals(1, $crawler->filter('a:contains("Norges teknisk-naturvitenskapelige universitet")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Universitetet i Bergen")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Norges miljø- og biovitenskapelige universitet")')->count());
        $this->assertEquals(1, $crawler->filter('a:contains("Universitetet i Oslo")')->count());
    }

    public function testCreateDepartment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/kontrollpanel/avdelingadmin');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Find a link and click it
        $link = $crawler->selectLink('Opprett avdeling')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createDepartment[name]'] = 'Ålesund';
        $form['createDepartment[shortName]'] = 'Ål';
        $form['createDepartment[email]'] = 'abcde@vektorprogrammet.no';
        $form['createDepartment[address]'] = 'Gata 9';
        $form['createDepartment[city]'] = 'Ålesund';

        // submit the form
        $crawler = $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/avdelingadmin'));

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we created a new entity
        $this->assertContains('Ålesund', $client->getResponse()->getContent());
        $this->assertContains('Ål', $client->getResponse()->getContent());
        $this->assertContains('abcde@vektorprogrammet.no', $client->getResponse()->getContent());

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('a:contains("Ålesund")')->count());
        $this->assertEquals(2, $crawler->filter('td:contains("Ål")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("abcde@vektorprogrammet.no")')->count());
    }

    public function testCreateDepartmentWithNonUniqueName()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/kontrollpanel/avdelingadmin');

        // Find a link and click it
        $link = $crawler->selectLink('Opprett avdeling')->link();
        $crawler = $client->click($link);

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createDepartment[name]'] = 'Heggen';
        $form['createDepartment[shortName]'] = 'Hgn';
        $form['createDepartment[email]'] = 'Hgn@mail.com';
        $form['createDepartment[address]'] = 'Storgata 9';
        $form['createDepartment[city]'] = 'Trondheim'; // Not unique!

        // submit the form
        $client->submit($form);

        // Assert not redirected
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert body contains validation error
        $this->assertContains('This value is already used', $client->getResponse()->getContent());
    }

    public function testUpdateDepartment()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));
        $crawler = $client->request('GET', '/kontrollpanel/avdelingadmin');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Find a link and click it
        $link = $crawler->selectLink('Rediger')->first()->link();
        $crawler = $client->click($link);

        // Assert that we have the correct page
        $this->assertEquals(1, $crawler->filter('h1:contains("Opprett avdeling")')->count());

        $form = $crawler->selectButton('Opprett')->form();

        // Change the value of a field
        $form['createDepartment[name]'] = 'Norges teknisk-naturvitenskapelige universitet2';
        $form['createDepartment[shortName]'] = 'NTNU2';
        $form['createDepartment[email]'] = 'NTNU@mail.com2';
        $form['createDepartment[address]'] = 'Storgata 1';

        // submit the form
        $crawler = $client->submit($form);

        // Assert a specific 302 status code
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Assert that the response is the correct redirect
        $this->assertTrue($client->getResponse()->isRedirect('/kontrollpanel/avdelingadmin'));

        // Follow the redirect
        $crawler = $client->followRedirect();

        // Assert that we created a new entity
        $this->assertContains('Norges teknisk-naturvitenskapelige universitet2', $client->getResponse()->getContent());
        $this->assertContains('NTNU2', $client->getResponse()->getContent());
        $this->assertContains('NTNU@mail.com2', $client->getResponse()->getContent());

        // Check the count for the different variables
        $this->assertEquals(1, $crawler->filter('a:contains("Norges teknisk-naturvitenskapelige universitet2")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("NTNU2")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("NTNU@mail.com2")')->count());

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /*
    Requires JQuery interaction, Symfony2 does not support that

    Phpunit was designed to test the PHP language, have to use another tool to test these.

    public function testDeleteDepartmentByIdAction() {}

    */
}
