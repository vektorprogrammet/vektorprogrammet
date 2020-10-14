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

        $form['application[yearOfStudy]'] = 3;
        $form['application[days][monday]']->tick();
        $form['application[days][tuesday]']->untick();
        $form['application[days][wednesday]']->tick();
        $form['application[days][thursday]']->untick();
        $form['application[days][friday]']->tick();
        $form['application[doublePosition]'] = 0;
        $form['application[preferredGroup]'] = 'Bolk 1';
        $form['application[language]'] = 'Engelsk';
        $form['application[preferredSchool]'] = $preferredSchool;

        $assistantClient->submit($form);
    }

	public function testDeniedIfNotHasBeenAssistant()
	{
		$crawler = $this->teamMemberGoTo('/eksisterendeopptak');

		$this->assertEquals(1, $crawler->filter('h5:contains("Fant ingen assistenthistorikk for din bruker")')->count());
	}
}
