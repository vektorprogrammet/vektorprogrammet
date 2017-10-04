<?php

namespace AppBundle\Google;

use AppBundle\Entity\Team;
use AppBundle\Entity\User;
use Google_Client;
use Google_Service_Directory;
use Google_Service_Directory_User;
use Google_Service_Directory_UserName;
use Google_Service_Drive;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class GoogleAPI
{
    private $refreshToken;
    private $clientId;
    private $clientSecret;
    private $credentialsPath;
    private $disabled;
    private $logger;

    public function __construct(array $apiOptions, LoggerInterface $logger)
    {
        $this->refreshToken    = $apiOptions['refresh_token'];
        $this->clientId        = $apiOptions['client_id'];
        $this->clientSecret    = $apiOptions['client_secret'];
        $this->disabled    = $apiOptions['disabled'];
        $this->credentialsPath = __DIR__ . '/credentials.json';
        $this->logger = $logger;
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

        $optParams = array(
            'customer' => 'my_customer',
        );

        try {
            $results = $service->users->listUsers($optParams);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "getUser('$companyEmail')");
            return null;
        }

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
        try {
            $results = $service->groups->listGroups($optParams);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "getGroups()");
            return [];
        }

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
     * @return \Google_Service_Directory_Group
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

        try {
            $createdGroup = $service->groups->insert($googleGroup);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "createTeam(), *{$team->getDepartment()} - $team*");
            return null;
        }

        return $createdGroup;
    }

    /**
     * @param string $groupEmail
     * @param Team $team
     * @return \Google_Service_Directory_Group
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

        try {
            $updatedTeam =  $service->groups->update($groupEmail, $googleGroup);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "updateTeam('$groupEmail') for *{$team->getDepartment()} - $team*");
            return null;
        }

        return $updatedTeam;
    }

    public function addUserToGroup(User $user, Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        $member = new \Google_Service_Directory_Member();
        $member->setType('GROUP');
        $member->setRole('MEMBER');
        $member->setEmail($user->getCompanyEmail());

        try {
            $service->members->insert($team->getEmail(), $member);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "addUserToGroup(), user *$user* to group *{$team->getDepartment()} - $team*");
        }
    }

    /**
     * @param Team $team
     *
     * @return \Google_Service_Directory_Member[]
     */
    public function getUsersInGroup(Team $team)
    {
        if ($this->disabled) {
            return [];
        }

        $client  = $this->getClient();
        $service = new Google_Service_Directory($client);

        try {
            $members = $service->members->listMembers($team->getEmail())->getMembers();
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "getUsersInGroup() from team *{$team->getDepartment()} - $team*");
            return [];
        }

        return $members;
    }

    public function userIsInGroup(User $user, Team $team)
    {
        if ($this->disabled) {
            return false;
        }

        $usersInGroup = $this->getUsersInGroup($team);
        foreach ($usersInGroup as $userInGroup) {
            if ($userInGroup->getEmail() === $user->getCompanyEmail()) {
                return true;
            }
        }

        return false;
    }

    public function createTeamDrive(Team $team)
    {
        if ($this->disabled) {
            return null;
        }
        $folderName = $team->getDepartment()->getShortName() . ' - ' . $team->getName();

        $client  = $this->getClient();
        $driveService = new \Google_Service_Drive($client);

        $requestId = Uuid::uuid4()->toString();
        $teamDriveMetadata = new \Google_Service_Drive_TeamDrive(array(
            'name' => $folderName));

        try {
            $teamDrive = $driveService->teamdrives->create($requestId, $teamDriveMetadata, array(
                'fields' => 'id' ));
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "createTeamDrive() for *$folderName*");
            return null;
        }

        $permission = new \Google_Service_Drive_Permission();
        $permission->setType('group');
        $permission->setRole('writer');
        $permission->setEmailAddress($team->getEmail());

        try {
            $driveService->permissions->create($teamDrive->id, $permission, array(
                'sendNotificationEmail' => false,
                'supportsTeamDrives' => true,
            ));
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "granting drive persmissions for team *$folderName*");
            return null;
        }

        return $teamDrive;
    }

    public function getAllEmailsInUse()
    {
        if ($this->disabled) {
            return [];
        }
        $users = $this->getUsers();
        $groups = $this->getGroups();

        $emailsInUse = [];
        foreach ($users as $user) {
            foreach ($user->getEmails() as $email) {
                $emailsInUse[] = $email['address'];
            }
        }

        foreach ($groups as $group) {
            $emailsInUse[] = $group->getEmail();
            $aliases = $group->getAliases();
            if ($aliases !== null) {
                $emailsInUse = array_merge($emailsInUse, $aliases);
            }
        }

        return $emailsInUse;
    }

    private function getClient()
    {
        $client = new Google_Client();
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->clientSecret);
        $client->setScopes(array(
            Google_Service_Directory::ADMIN_DIRECTORY_USER,
            Google_Service_Directory::ADMIN_DIRECTORY_GROUP,
            Google_Service_Drive::DRIVE
        ));

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

    private function logServiceException(\Google_Service_Exception $exception, string $message)
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
