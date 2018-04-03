<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AdmissionControllerTest extends BaseWebTestCase
{
    public function testCreateApplication()
    {
        //TODO: Update to redesign
//        $path = '/kontrollpanel/opptak';
//
//        $applicationsBefore = $this->countTableRows($path);
//
//        $this->createAndSubmitForm('Peter');
//
//        $applicationsAfter = $this->countTableRows($path);
//
//        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testCreateWantTeamInterest()
    {
        $path = '/kontrollpanel/opptakadmin/teaminteresse/2';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm_teamInterest(true);

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testCreateNotWantTeamInterest()
    {
        $path = '/kontrollpanel/opptakadmin/teaminteresse/2';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm_teamInterest(false);

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore, $applicationsAfter);
    }


    public function testCreateWithPreferredSchool()
    {
        $this->createAndSubmitForm_preferredSchool('');

        $path = '/kontrollpanel/opptakadmin/soknad/110';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm_preferredSchool('Gimse');

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }


    /**
     * @param string $testname
     */
    private function createAndSubmitForm(string $testname)
    {
        $crawler = $this->assistantGoTo('/opptak/avdeling/1');

        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[user][firstName]'] = $testname;
        $form['application[user][lastName]'] = $testname;
        $form['application[user][email]'] = 'test@vektorprogrammet.no';
        $form['application[user][phone]'] = '99887766';
        $form['application[user][fieldOfStudy]'] = 2;
        $form['application[user][gender]'] = 0;
        $form['application[yearOfStudy]'] = 4;

        self::createAssistantClient()->submit($form);
    }

    /**
     * @param bool $teamInterest
     */
    private function createAndSubmitForm_teamInterest(bool $teamInterest)
    {
        $crawler = $this->assistantGoTo('/opptak/eksisterende');
        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[applicationPractical][yearOfStudy]'] = 3;
        $form['application[applicationPractical][days][monday]']->tick();
        $form['application[applicationPractical][days][tuesday]']->untick();
        $form['application[applicationPractical][days][wednesday]']->tick();
        $form['application[applicationPractical][days][thursday]']->untick();
        $form['application[applicationPractical][days][friday]']->tick();
        $form['application[applicationPractical][doublePosition]'] = 0;
        $form['application[applicationPractical][preferredGroup]'] = 'Bolk 1';
        $form['application[applicationPractical][language]'] = 'Engelsk';
        $form['application[applicationPractical][teamInterest]'] = $teamInterest;

        self::createAssistantClient()->submit($form);
    }

    /**
     * @param string $preferredSchool
     */
    private function createAndSubmitForm_preferredSchool(string $preferredSchool)
    {
        $assistantClient = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $this->goTo('/opptak/eksisterende', $assistantClient);
        $submitButton = $crawler->selectButton('Søk');
        $form = $submitButton->form();

        $form['application[applicationPractical][yearOfStudy]'] = 3;
        $form['application[applicationPractical][days][monday]']->tick();
        $form['application[applicationPractical][days][tuesday]']->untick();
        $form['application[applicationPractical][days][wednesday]']->tick();
        $form['application[applicationPractical][days][thursday]']->untick();
        $form['application[applicationPractical][days][friday]']->tick();
        $form['application[applicationPractical][doublePosition]'] = 0;
        $form['application[applicationPractical][preferredGroup]'] = 'Bolk 1';
        $form['application[applicationPractical][language]'] = 'Engelsk';
        $form['application[applicationPractical][teamInterest]'] = '0';
        $form['application[preferredSchool]'] = $preferredSchool;

        $crawler = $assistantClient->submit($form);
    }
}
