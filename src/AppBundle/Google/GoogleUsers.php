<?php

namespace AppBundle\Google;

use AppBundle\Entity\User;
use Google_Service_Directory;
use Google_Service_Directory_User;
use Google_Service_Directory_UserName;

class GoogleUsers extends GoogleService
{

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
        try {
            $results = $service->users->listUsers($optParams);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "getUsers()");
            return [];
        }

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

        try {
            return $service->users->get($companyEmail);
        } catch (\Google_Service_Exception $e) {
            if ($e->getCode() !== 404) {
                $this->logServiceException($e, "getUser('$companyEmail')");
            }
            return null;
        }
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

        try {
            $createdUser = $service->users->insert($googleUser);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "createUser() for $user");
            return null;
        }
        return $createdUser;
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

        try {
            $updatedUser = $service->users->update($userKey, $googleUser);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "updateUser() $userKey ($user)");
            return null;
        }

        return $updatedUser;
    }

    /**
     * @param string $userKey
     * @return void
     */
    public function deleteUser(string $userKey)
    {
        if ($this->disabled) {
            return;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);
        try {
            $service->users->delete($userKey);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "deleteUser('$userKey')");
        }
    }
}
