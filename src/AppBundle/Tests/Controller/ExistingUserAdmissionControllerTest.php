<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class ExistingUserAdmissionControllerTest extends BaseWebTestCase
{
    public function testCreateApplication()
    {
        //TODO: Update to redesign
        // Move to assistant controller test
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
     * @param string $preferredSchool
     */
    private function createAndSubmitForm_preferredSchool(string $preferredSchool)
    {
        $assistantClient = self::createClient(array(), array(
            'PHP_AUTH_USER' => 'petjo',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $this->goTo('/eksisterendeopptak', $assistantClient);
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
        $form['application[preferredSchool]'] = $preferredSchool;

        $crawler = $assistantClient->submit($form);
    }
}