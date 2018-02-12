<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AdmissionControllerTest extends BaseWebTestCase
{
    public function testShow()
    {
    }

    public function testCreateWantNewsletterApplication()
    {
        $path = '/kontrollpanel/nyhetsbrev/abonnenter/4';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm('Love newsletters Peter', true);

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    public function testCreateNotWantNewsletterApplication()
    {
        $path = '/kontrollpanel/nyhetsbrev/abonnenter/4';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm('No newsletter Johnson', false);

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore, $applicationsAfter);
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

    /**
     * @param string $testname
     * @param bool   $wantsNewsletter
     */
    private function createAndSubmitForm(string $testname, bool $wantsNewsletter)
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
        $form['application[wantsNewsletter]'] = $wantsNewsletter;

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
}
