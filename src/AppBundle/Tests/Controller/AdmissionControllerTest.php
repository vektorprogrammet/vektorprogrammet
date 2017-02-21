<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdmissionControllerTest extends WebTestCase
{
    public function testShow()
    {
    }

    public function testCreateWantNewsletterApplication()
    {
        $applicationsBefore = $this->countRows();

        // Submit an application
        // User
        $clientAnonymous = static::createClient();

        $crawler = $clientAnonymous->request('GET', '/opptak/avdeling/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[user][firstName]'] = 'Newsletter Please';
        $form['application[user][lastName]'] = 'Newsletter Please';
        $form['application[user][email]'] = 'j@vektorprogrammet.no';
        $form['application[user][phone]'] = '99887766';
        $form['application[user][fieldOfStudy]'] = 2;
        $form['application[user][gender]'] = 0;
        $form['application[yearOfStudy]'] = 4;
        $form['application[wantsNewsletter]'] = true;

        $clientAnonymous->submit($form);

        $applicationsAfter = $this->countRows();

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    public function testCreateNotWantNewsletterApplication()
    {
        $applicationsBefore = $this->countRows();
        // Submit an application
        // User
        $clientAnonymous = static::createClient();

        $crawler = $clientAnonymous->request('GET', '/opptak/avdeling/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[user][firstName]'] = 'No newsletters please';
        $form['application[user][lastName]'] = 'No newsletters please';
        $form['application[user][email]'] = 'j@vektorprogrammet.no';
        $form['application[user][phone]'] = '99887766';
        $form['application[user][fieldOfStudy]'] = 2;
        $form['application[user][gender]'] = 0;
        $form['application[yearOfStudy]'] = 4;
        $form['application[wantsNewsletter]'] = false;

        $clientAnonymous->submit($form);

        $applicationsAfter = $this->countRows();

        $this->assertEquals($applicationsBefore, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    private function countRows()
    {
        // Admin
        $clientAdmin = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        // Go to AdmissionTestList
        $crawler = $clientAdmin->request('GET', '/kontrollpanel/nyhetsbrev/4');
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        return $crawler->filter('tr')->count();
    }
}
