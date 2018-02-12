<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class NewsletterControllerTest extends BaseWebTestCase
{
    public function testShowWithActiveAdmission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/1');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('p:contains("Du kan melde deg på ")')->count());
    }

    public function testShowWithoutActiveAdmission()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/2');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('p:contains("Du kan melde deg på ")')->count());
        $this->assertEquals(1, $crawler->filter('h4:contains("har ikke aktiv søkeperiode")')->count());
    }

    public function testShowWithActiveNewsletter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('p:contains("Du kan melde deg på ")')->count());
        $this->assertEquals(1, $crawler->filter('h4:contains("har ikke aktiv søkeperiode")')->count());
    }

    public function testNewsletterLinkInControlPanel()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertGreaterThanOrEqual(3, $crawler->filter('a:contains("Nyhetsbrev")')->count());
    }

    public function testShowSpecificNewsletter()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('a:contains("Rediger nyhetsbrevet")')->count());
        $this->assertEquals(1, $crawler->filter('button:contains("Slett nyhetsbrevet")')->count());
        $this->assertGreaterThanOrEqual(2, $crawler->filter('button:contains("Slett")')->count());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('a:contains("Rediger nyhetsbrevet")')->count());
        $this->assertEquals(0, $crawler->filter('button:contains("Slett nyhetsbrevet")')->count());
        $this->assertEquals(0, $crawler->filter('button:contains("Slett")')->count());
    }

    public function testShowNewsletterPermission()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(1, $crawler->filter('a:contains("Opprett nytt nyhetsbrev")')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('a:contains("Lag brev")')->count());
        $this->assertEquals(1, $crawler->filter('th:contains("Vis på påmeldingsside")')->count());

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'team',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $this->assertTrue($client->getResponse()->isSuccessful());

        // Assert that we have the correct amount of data
        $this->assertEquals(0, $crawler->filter('a:contains("opprett nytt nyhetsbrev")')->count());
        $this->assertEquals(0, $crawler->filter('a:contains("Lag brev")')->count());
        $this->assertEquals(0, $crawler->filter('th:contains("Vis på påmeldingsside")')->count());
    }

    public function testShowOnAdmissionPage()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->selectButton('Aktiv')->count());
        $activateButtons = $crawler->selectButton('Aktiver');
        $form = $activateButtons->eq(0)->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(1, $crawler->selectButton('Aktiv')->count());

        $allButtons = $crawler->filter('button:contains("Aktiv")');

        $this->assertEquals('Aktiv', $allButtons->eq(0)->html());
        $form = $allButtons->eq(1)->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $allButtons = $crawler->filter('button:contains("Aktiv")');
        $this->assertEquals(1, $crawler->selectButton('Aktiv')->count());
        $this->assertEquals('Aktiver', $allButtons->eq(0)->html());
        $this->assertEquals('Aktiv', $allButtons->eq(1)->html());
    }

    public function testSubscribeOnAdmissionPage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/opptak/avdeling/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $button = $crawler->selectButton('Registrer');

        $form = $button->form();

        $form['app_bundle_subscribe_to_newsletter_type[name]'] = 'Karl';
        $form['app_bundle_subscribe_to_newsletter_type[email]'] = 'user@user.com';

        $client->submit($form);

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'nmbu',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('td:contains("Karl")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("user@user.com")')->count());
    }

    public function testSubscribePage()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $button = $crawler->selectButton('Registrer');

        $form = $button->form();

        $form['app_bundle_subscribe_to_newsletter_type[name]'] = 'Karl';
        $form['app_bundle_subscribe_to_newsletter_type[email]'] = 'user@user.com';

        $client->submit($form);

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'nmbu',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('td:contains("Karl")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("user@user.com")')->count());
    }

    public function testSubscribeMultipleIdenticalEmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $button = $crawler->selectButton('Registrer');

        $form = $button->form();

        $form['app_bundle_subscribe_to_newsletter_type[name]'] = 'Karl';
        $form['app_bundle_subscribe_to_newsletter_type[email]'] = 'user@user.com';

        $client->submit($form);

        $crawler = $client->request('GET', '/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $button = $crawler->selectButton('Registrer');

        $form = $button->form();

        $form['app_bundle_subscribe_to_newsletter_type[name]'] = 'Karl';
        $form['app_bundle_subscribe_to_newsletter_type[email]'] = 'user@user.com';

        $client->submit($form);

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'nmbu',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/3');

        // Assert a specific 200 status code
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('td:contains("Karl")')->count());
        $this->assertEquals(1, $crawler->filter('td:contains("user@user.com")')->count());
    }

    public function testDeleteSubscriber()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/1');

        $deleteButtonsBefore = $crawler->selectButton('Slett')->count();

        $activateButtons = $crawler->selectButton('Slett');
        $form = $activateButtons->eq(1)->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $deleteButtonsAfter = $crawler->selectButton('Slett')->count();

        $this->assertEquals($deleteButtonsAfter, $deleteButtonsBefore - 1);
    }

    public function testExcludeApplicant()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/send/1');
        $button = $crawler->selectButton('Send');
        $form = $button->form();
        $form['app_bundle_create_letter_type[title]'] = 'Test';
        $form['app_bundle_create_letter_type[excludeApplicants]']->tick();
        $form['app_bundle_create_letter_type[content]'] = 'Test';

        $client->enableProfiler();
        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $messages = $mailCollector->getMessages();

        $found = false;
        foreach ($messages as $message){
            if (key($message->getTo()) ==  'assistant@gmail.com'){
                $found = true;
            }
        }

        $this->assertNotTrue($found);
    }

    public function testIncludeApplicant()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ));

        $crawler = $client->request('GET', '/kontrollpanel/nyhetsbrev/send/1');
        $button = $crawler->selectButton('Send');
        $form = $button->form();
        $form['app_bundle_create_letter_type[title]'] = 'Test';
        $form['app_bundle_create_letter_type[excludeApplicants]']->untick();
        $form['app_bundle_create_letter_type[content]'] = 'Test';

        $client->enableProfiler();
        $client->submit($form);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $messages = $mailCollector->getMessages();

        $found = false;
        foreach ($messages as $message){
            if (key($message->getTo()) == 'assistant@gmail.com'){
                $found = true;
            }
        }

        $this->assertTrue($found);
    }
}
