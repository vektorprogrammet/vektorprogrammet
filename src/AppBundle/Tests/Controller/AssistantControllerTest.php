<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class AssistantControllerTest extends BaseWebTestCase
{
    public function testCreateApplication()
    {
        $path = '/kontrollpanel/opptak';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm();

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    private function createAndSubmitForm()
    {
        $crawler = $this->anonymousGoTo('/opptak/avdeling/1');

        $form = $crawler->filter('form[name="application_1"] button:contains("Søk")')->form();

        $form['application_1[user][firstName]'] = 'test';
        $form['application_1[user][lastName]'] = 'mctest';
        $form['application_1[user][email]'] = 'test@vektorprogrammet.no';
        $form['application_1[user][phone]'] = '99887766';
        $form['application_1[user][gender]'] = 0;
        $form['application_1[user][fieldOfStudy]'] = 2;
        $form['application_1[yearOfStudy]'] = '1. klasse';

        $this->createAnonymousClient()->submit($form);
    }
}
