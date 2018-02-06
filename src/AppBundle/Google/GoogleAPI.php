<?php

namespace AppBundle\Google;

class GoogleAPI
{
    private $userService;
    private $groupService;
    private $disabled;

    public function __construct(array $apiOptions, GoogleUsers $userService, GoogleGroups $groupService)
    {
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->disabled = $apiOptions['disabled'];
    }

    public function getAllEmailsInUse()
    {
        if ($this->disabled) {
            return [];
        }
        $users = $this->userService->getUsers();
        $groups = $this->groupService->getGroups();

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
}
