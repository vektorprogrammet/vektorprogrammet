<?php

declare(strict_types=1);

namespace App\Support\Infrastructure\Google;

use App\Organization\Infrastructure\Entity\Team;
use App\Identity\Infrastructure\Entity\User;

class GoogleGroups extends GoogleService
{
    /**
     * @param int|null $maxResults
     *
     * @return \Google_Service_Directory_Group[]
     */
    public function getGroups(?int $maxResults = null)
    {
        if ($this->disabled) {
            return [];
        }

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

        $optParams = [
            'customer' => 'my_customer',
        ];

        if ($maxResults !== null) {
            $optParams['maxResults'] = $maxResults;
        }
        try {
            $results = $service->groups->listGroups($optParams);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, 'getGroups()');

            return [];
        }

        return $results->getGroups();
    }

    /**
     * @return \Google_Service_Directory_Group|null
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
     * @return \Google_Service_Directory_Group|null
     */
    public function createGroup(Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

        $googleGroup = new \Google_Service_Directory_Group();
        $this->setGroupNameEmailDescription($googleGroup, $team);

        try {
            $createdGroup = $service->groups->insert($googleGroup);
        } catch (\Google_Service_Exception $e) {
            $this->logServiceException($e, "createTeam(), *{$team->getDepartment()} - $team*");

            return null;
        }

        return $createdGroup;
    }

    /**
     * @return \Google_Service_Directory_Group|null
     */
    public function updateGroup(string $groupEmail, Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

        $googleGroup = new \Google_Service_Directory_Group();
        $this->setGroupNameEmailDescription($googleGroup, $team);

        try {
            $updatedTeam = $service->groups->update($groupEmail, $googleGroup);
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

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

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

    public function removeUserFromGroup(User $user, Team $team)
    {
        if ($this->disabled) {
            return null;
        }

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

        $usersInGroup = $this->getUsersInGroup($team);

        foreach ($usersInGroup as $member) {
            if ($member->getEmail() === $user->getCompanyEmail()) {
                try {
                    $service->members->delete($team->getEmail(), $member->getEmail());
                } catch (\Google_Service_Exception $e) {
                    $this->logServiceException($e, "removeUserFromGroup(), user *$user* to group *{$team->getDepartment()} - $team*");
                }
                break;
            }
        }
    }

    /**
     * @return \Google_Service_Directory_Member[]
     */
    public function getUsersInGroup(Team $team)
    {
        if ($this->disabled) {
            return [];
        }

        $client = $this->getClient();
        $service = new \Google_Service_Directory($client);

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

    private function setGroupNameEmailDescription(\Google_Service_Directory_Group $googleGroup, Team $team)
    {
        $googleGroup->setName($team->getName().' - '.$team->getDepartment());
        $googleGroup->setEmail($team->getEmail());
        $googleGroup->setDescription($team->getShortDescription());
    }
}
