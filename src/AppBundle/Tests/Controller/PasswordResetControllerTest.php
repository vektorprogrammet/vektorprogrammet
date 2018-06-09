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

        $this->assertTrue($this->loginSuccessful(self::newPass));
        $this->assertFalse($this->loginSuccessful(self::oldPass));
    }

    /**
     * @param string $password
     *
     * @return bool login successful
     */
    private function loginSuccessful($password)
    {
        $crawler = $this->anonymousGoTo('/login');

        $form = $crawler->selectButton('Logg inn')->form();
        $form['_username'] = self::username;
        $form['_password'] = $password;
        $client = $this->createAnonymousClient();
        $client->submit($form);

        $crawler = $client->request('GET', '/');
        return $crawler->filter('nav span:contains("Logg inn")')->count() == 0;
    }
}
