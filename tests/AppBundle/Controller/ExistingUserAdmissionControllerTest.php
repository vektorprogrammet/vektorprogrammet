<?php

namespace Tests\AppBundle\Controller;

use Tests\BaseWebTestCase;

class ExistingUserAdmissionControllerTest extends BaseWebTestCase
{
    public function testCreateWithPreferredSchool()
    {
        $path = '/kontrollpanel/opptak/gamle';

        $applicationsBefore = $this->countTableRows($path);

        $this->createAndSubmitForm_preferredSchool('Gimse');

        $applicationsAfter = $this->countTableRows($path);

        $this->assertEquals($applicationsBefore + 1, $applicationsAfter);
    }

    /**
     * @param string $preferredSchool
     */
    private function createAndSubmitForm_preferredSchool(string $preferredSchool)
    {
        $assistantClient = $this->createAssistantClient();

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

        $assistantClient->submit($form);
    }

	public function testDeniedIfNotHasBeenAssistant()
	{
		$crawler = $this->teamMemberGoTo('/eksisterendeopptak');

		$this->assertEquals(1, $crawler->filter('h5:contains("Fant ingen assistenthistorikk for din bruker")')->count());
	}
}
