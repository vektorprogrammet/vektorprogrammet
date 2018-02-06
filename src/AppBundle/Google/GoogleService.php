<?php

namespace AppBundle\Google;

use Google_Client;
use Google_Service_Directory;
use Google_Service_Drive;
use Psr\Log\LoggerInterface;

abstract class GoogleService
{
    private $refreshToken;
    private $clientId;
    private $clientSecret;
    private $credentialsPath;
    protected $disabled;
    protected $logger;

    public function __construct(array $apiOptions, LoggerInterface $logger)
    {
        $this->refreshToken = $apiOptions['refresh_token'];
        $this->clientId = $apiOptions['client_id'];
        $this->clientSecret = $apiOptions['client_secret'];
        $this->disabled = $apiOptions['disabled'];
        $this->credentialsPath = __DIR__ . '/credentials.json';
        $this->logger = $logger;
    }


    /**
     * @return Google_Client
     */
    protected function getClient()
    {
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setScopes(array(
            Google_Service_Directory::ADMIN_DIRECTORY_USER,
            Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
            Google_Service_Drive::DRIVE,
            \Google_Service_Gmail::GMAIL_SEND
        ));

        if (file_exists($this->credentialsPath)) {
            $accessToken = json_decode(file_get_contents($this->credentialsPath), true);
            $client->setAccessToken($accessToken);
        }

        // Refresh the token if it's expired.
        if (!file_exists($this->credentialsPath) || $client->isAccessTokenExpired()) {
            $accessToken = $client->fetchAccessTokenWithRefreshToken($this->refreshToken);
            if ($accessToken) {
                file_put_contents($this->credentialsPath, json_encode($client->getAccessToken()));
            }
        }

        return $client;
    }

    protected function logServiceException(\Google_Service_Exception $exception, string $message)
    {
        $this->logger->critical(
            "Google_Service_Exception caught: $message\n".
            "`Code: " . $exception->getCode() . "`\n".
            "```\n".
            $exception->getMessage(). "\n".
            "´´´"
        );
    }
}
