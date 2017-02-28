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
        $path = '/kontrollpanel/nyhetsbrev/4';

        $applicationsBefore = $this->countRows($path);

        $this->createAndSubmitForm('Love newsletters Peter', true);

        $applicationsAfter = $this->countRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    public function testCreateNotWantNewsletterApplication()
    {
        $path = '/kontrollpanel/nyhetsbrev/4';

        $applicationsBefore = $this->countRows($path);

        $this->createAndSubmitForm('No newsletter Johnson', false);

        $applicationsAfter = $this->countRows($path);

        $this->assertEquals($applicationsBefore, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    public function testCreateWantTeamInterest()
    {
        $path = '/kontrollpanel/opptakadmin/teaminteresse/2';

        $applicationsBefore = $this->countRows($path);

        $this->createAndSubmitForm_teamInterest(true);

        $applicationsAfter = $this->countRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    public function testCreateNotWantTeamInterest()
    {
        $path = '/kontrollpanel/opptakadmin/teaminteresse/2';

        $applicationsBefore = $this->countRows($path);

        $this->createAndSubmitForm_teamInterest(false);

        $applicationsAfter = $this->countRows($path);

        $this->assertEquals($applicationsBefore, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public function countRows(string $path):int
    {
        // Admin
        $clientAdmin = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        // Go to AdmissionTestList
        $crawler = $clientAdmin->request('GET', $path);
        $this->assertTrue($clientAdmin->getResponse()->isSuccessful());

        return $crawler->filter('tr')->count();
    }

    /**
     * @param string $testname
     * @param bool   $wantsNewsletter
     */
    private function createAndSubmitForm(string $testname, bool $wantsNewsletter)
    {
        $clientAnonymous = static::createClient();

        $crawler = $clientAnonymous->request('GET', '/opptak/avdeling/1');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[user][firstName]'] = $testname;
        $form['application[user][lastName]'] = $testname;
        $form['application[user][email]'] = 'test@vektorprogrammet.no';
        $form['application[user][phone]'] = '99887766';
        $form['application[user][fieldOfStudy]'] = 2;
        $form['application[user][gender]'] = 0;
        $form['application[yearOfStudy]'] = 4;
        $form['application[wantsNewsletter]'] = $wantsNewsletter;

        $clientAnonymous->submit($form);
    }

    /**
     * @param bool $teamInterest
     */
    private function createAndSubmitForm_teamInterest(bool $teamInterest)
    {
        $clientAnonymous = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'assistent',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $clientAnonymous->request('GET', '/opptak/eksisterende');
        $this->assertTrue($clientAnonymous->getResponse()->isSuccessful());

        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[yearOfStudy]'] = 3;
        $form['application[applicationPractical][monday]'] = 'Bra';
        $form['application[applicationPractical][tuesday]'] = 'Ikke';
        $form['application[applicationPractical][wednesday]'] = 'Bra';
        $form['application[applicationPractical][thursday]'] = 'Ikke';
        $form['application[applicationPractical][friday]'] = 'Bra';
        $form['application[applicationPractical][doublePosition]'] = 0;
        $form['application[applicationPractical][preferredGroup]'] = 'Bolk 1';
        $form['application[applicationPractical][english]'] = 1;
        $form['application[applicationPractical][teamInterest]'] = $teamInterest;

        $clientAnonymous->submit($form);
    }
}
