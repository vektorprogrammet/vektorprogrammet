<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class PasswordResetControllerTest extends BaseWebTestCase
{
    const epost = 'admin@gmail.com';
    const brukernavn = 'admin';
    const gammeltPassord = '1234';
    const nyttPassord = '12345678';


    public function testResetPasswordAction()
    {
        //Lager crawler og sjekker om vi har kommet til riktig side
        $crawler = $this->anonymousGoTo('/resetpassord');
        $this->assertEquals(1, $crawler->filter('h1:contains("Tilbakestill passord")')->count());

        //Fyller inn epost i formen og submiter
        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = self::epost;
        $client = $this->createAnonymousClient();
        $client->enableProfiler();
        $client->submit($form);

        //Sjekker om det blir sendt epost
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $message = $mailCollector->getMessages()[0];
        $body = $message->getBody();

        //Henter ut reset-urlen
        $start = strpos($body, '/resetpassord/');
        $messageStartingWithCode = substr($body, $start);
        $end = strpos($messageStartingWithCode, "\n");
        $substring = substr($body, $start, $end);

        //Tilbakestiller passord
        $crawler = $this->anonymousGoTo($substring);
        $this->assertEquals(1, $crawler->filter('h1:contains("Lag nytt passord")')->count());
        $form = $crawler->selectButton('Lagre nytt passord')->first()->form();
        $form['newPassword[password][first]'] = self::nyttPassord;
        $form['newPassword[password][second]'] = self::nyttPassord;
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        //Prøver å logge inn med gammelt og nytt passord
        $this->LoginNewPassword();
        $this->LoginOldPassword();
        
    }

    private function LoginNewPassword()
    {
        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => self::brukernavn,
            'PHP_AUTH_PW' => self::nyttPassord,
        ));
        $crawler = $this->goTo('/', $client);
        $this->assertEquals(0, $crawler->filter('nav:contains("Logg inn")')->count());
    }

    private function LoginOldPassword()
    {
        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => self::brukernavn,
            'PHP_AUTH_PW' => self::gammeltPassord,
        ));
        
        $client->request('GET', '/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        
        $this->assertEquals(1, $crawler->filter('nav:contains("Logg inn")')->count());
    }
}
