<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\BaseWebTestCase;

class PasswordResetControllerTest extends BaseWebTestCase
{
    private const email = 'admin@gmail.com';
    private const username = 'admin';
    private const oldPass = '1234';
    private const newPass = '12345678';

    public function testResetPasswordAction()
    {
        // Test that we're getting the right page
        $crawler = $this->anonymousGoTo('/resetpassord');
        $this->assertEquals(1, $crawler->filter('h1:contains("Tilbakestill passord")')->count());

        // Fill in form and
        $form = $crawler->selectButton('Tilbakestill passord')->form();
        $form['passwordReset[email]'] = self::email;
        $client = $this->createAnonymousClient();
        $client->enableProfiler();
        $client->submit($form);

        // Assert email sent
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $message = $mailCollector->getMessages()[0];
        $body = $message->getBody();

        // Get reset link from email
        $start = strpos($body, '/resetpassord/');
        $messageStartingWithCode = substr($body, $start);
        $end = strpos($messageStartingWithCode, "\n");
        $substring = substr($body, $start, $end);

        // Reset password
        $crawler = $this->anonymousGoTo($substring);
        $this->assertEquals(1, $crawler->filter('h1:contains("Lag nytt passord")')->count());
        $form = $crawler->selectButton('Lagre nytt passord')->first()->form();
        $form['newPassword[password][first]'] = self::newPass;
        $form['newPassword[password][second]'] = self::newPass;
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // Try logging in with new pass
        $this->loginNewPassword();

        /*
         * Trying the same trick with a wrong password, such as the old one puts the
         * client in an infinite redirect loop
         */
    }

    private function loginNewPassword()
    {
        $client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => self::username,
            'PHP_AUTH_PW' => self::newPass,
        ));
        $crawler = $this->goTo('/', $client);
        $this->assertEquals(0, $crawler->filter('nav span:contains("Logg inn")')->count());
    }
}
