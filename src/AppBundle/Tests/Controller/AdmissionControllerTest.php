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

        $this->createAndSubmitForm('Love newsletters Peter', true);

        $applicationsAfter = $this->countRows();

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    public function testCreateNotWantNewsletterApplication()
    {
        $applicationsBefore = $this->countRows();

        $this->createAndSubmitForm('No newsletter Johnson', false);

        $applicationsAfter = $this->countRows();

        $this->assertEquals($applicationsBefore, $applicationsAfter);
        \TestDataManager::restoreDatabase();
    }

    /**
     * @return int
     */
    private function countRows():int
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
}
