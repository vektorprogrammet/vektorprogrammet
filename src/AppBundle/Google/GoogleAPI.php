<?php

namespace AppBundle\Google;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Google_Client;
use Google_Service_Directory;
use Google_Service_Directory_User;
use Google_Service_Directory_UserName;

class GoogleAPI
{
    private $refreshToken;
    private $clientId;
    private $clientSecret;
    private $credentialsPath;
    private $disabled;

    public function __construct(array $apiOptions)
    {
        $this->refreshToken    = $apiOptions['refresh_token'];
        $this->clientId        = $apiOptions['client_id'];
        $this->clientSecret    = $apiOptions['client_secret'];
        $this->disabled    = $apiOptions['disabled'];
        $this->credentialsPath = __DIR__ . '/credentials.json';
    }

    /**
     * @param null $maxResults
     * @return Google_Service_Directory_User[]
     */
    public function getUsers($maxResults = null)
    {
        if ($this->disabled) {
            return [];
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $optParams = array(
            'customer' => 'my_customer',
        );

        if ($maxResults) {
            $optParams['maxResults'] = $maxResults;
        }
        $results = $service->users->listUsers($optParams);

        return $results->getUsers();
    }

    /**
     * @param string $companyEmail
     * @return Google_Service_Directory_User
     */
    public function getUser(string $companyEmail)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $optParams = array(
            'customer' => 'my_customer',
        );

        $results = $service->users->listUsers($optParams);

        foreach ($results->getUsers() as $user) {
            if ($user->primaryEmail === $companyEmail) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @param User $user
     * @return Google_Service_Directory_User
     */
    public function createUser(User $user)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $googleUser = new Google_Service_Directory_User();
        $googleUser->setPrimaryEmail($user->getCompanyEmail());

        $password = bin2hex(random_bytes(15));
        $googleUser->setPassword($password);

        $name = new Google_Service_Directory_UserName();
        $name->setGivenName($user->getFirstName());
        $name->setFamilyName($user->getLastName());
        $name->setFullName($user->getFullName());
        $googleUser->setName($name);

        return $service->users->insert($googleUser);
    }

    /**
     * @param string $userKey
     * @param User $user
     * @return Google_Service_Directory_User
     */
    public function updateUser(string $userKey, User $user)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $googleUser = new Google_Service_Directory_User();
        $googleUser->setPrimaryEmail($user->getCompanyEmail());

        $name = new Google_Service_Directory_UserName();
        $name->setGivenName($user->getFirstName());
        $name->setFamilyName($user->getLastName());
        $name->setFullName($user->getFullName());
        $googleUser->setName($name);

        return $service->users->update($userKey, $googleUser);
    }

    /**
     * @param string $userKey
     * @return null
     */
    public function deleteUser(string $userKey)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);
        $service->users->delete($userKey);
    }

    /**
     * @param null $maxResults
     * @return \Google_Service_Directory_Group[]
     */
    public function getGroups($maxResults = null)
    {
        if ($this->disabled) {
            return [];
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $optParams = array(
            'customer' => 'my_customer',
        );

        if ($maxResults) {
            $optParams['maxResults'] = $maxResults;
        }
        $results = $service->groups->listGroups($optParams);

        return $results->getGroups();
    }

    /**
     * @param string $teamEmail
     * @return \Google_Service_Directory_Group
     */
    public function getGroup(string $teamEmail)
    {
        if ($this->disabled) {
            return null;
        }

        $groups = $this->getGroups();
        
        foreach ($groups as $group) {
            if ($group->getEmail() === $teamEmail) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @param Team $team
     * @return Google_Service_Directory_User
     */
    public function createGroup(Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);
        
        $googleGroup = new \Google_Service_Directory_Group();
        $googleGroup->setName($team->getName());
        $googleGroup->setEmail($team->getEmail());
        $googleGroup->setDescription($team->getShortDescription());

        return $service->groups->insert($googleGroup);
    }

    /**
     * @param string $groupEmail
     * @param Team $team
     * @return Google_Service_Directory_User
     */
    public function updateGroup(string $groupEmail, Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $googleGroup = new \Google_Service_Directory_Group();
        $googleGroup->setName($team->getName());
        $googleGroup->setEmail($team->getEmail());
        $googleGroup->setDescription($team->getShortDescription());

        return $service->groups->update($groupEmail, $googleGroup);
    }
    

    private function getClient()
    {
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setScopes(array( Google_Service_Directory::ADMIN_DIRECTORY_USER ));

        if (file_exists($this->credentialsPath)) {
            $accessToken = json_decode(file_get_contents($this->credentialsPath), true);
            $client->setAccessToken($accessToken);
        }

        // Refresh the token if it's expired.
        if (! file_exists($this->credentialsPath) || $client->isAccessTokenExpired()) {
            $accessToken = $client->fetchAccessTokenWithRefreshToken($this->refreshToken);
            if ($accessToken) {
                file_put_contents($this->credentialsPath, json_encode($client->getAccessToken()));
            }
        }

        return $client;
    }
}
