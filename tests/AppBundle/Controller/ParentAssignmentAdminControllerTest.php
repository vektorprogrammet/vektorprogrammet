<?php

use Tests\BaseWebTestCase;

class ParentAssignmentAdminControllerTest extends BaseWebTestCase
{
    public function testCreate()
    {
        $client = $this->createAdminClient();

        $crawler = $client->request('GET', '/kontrollpanel/foreldrekurs/pamelding/61');
        $form = $crawler->selectButton('Meld på!')->form();

        $date = (new \DateTime())->modify('+1day')->format('d.m.Y H:m');

        $form['parent_assignment[navn]'] = 'Ola Nordmann';
        $form['parent_assignment[epost]'] = 'ola@nordmann.no';
        #Må jeg teste at setCourse fungerer i selve controlleren også? (altså uten formen)

        $client->submit($form);

        $crawler = $client->followRedirect();
        $parentAssignmentAdminAfter = $crawler->filter('td:contains("Ola Nordmann")')->count();
        $this->assertEquals(1, $parentAssignmentAdminAfter);
        #InvalidArgumentException: The current node list is empty.
        # nå gidder jeg ikke mer!

    }


}
