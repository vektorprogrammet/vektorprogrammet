<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Event\TeamEvent;
use AppBundle\Event\UserEvent;
use AppBundle\Event\TeamMembershipEvent;
use AppBundle\Google\GoogleAPI;
use AppBundle\Google\GoogleDrive;
use AppBundle\Google\GoogleGroups;
use AppBundle\Google\GoogleUsers;
use AppBundle\Service\CompanyEmailMaker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GSuiteSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $googleAPI;
    private $emailMaker;
    private $userService;
    private $groupService;
    private $driveService;


    public function __construct(LoggerInterface $logger, GoogleAPI $googleAPI, CompanyEmailMaker $emailMaker, GoogleUsers $userService, GoogleGroups $groupService, GoogleDrive $driveService)
    {
        $this->logger = $logger;
        $this->googleAPI = $googleAPI;
        $this->emailMaker = $emailMaker;
        $this->userService = $userService;
        $this->groupService = $groupService;
        $this->driveService = $driveService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            TeamMembershipEvent::CREATED => array(
                array('createGSuiteUser', 1),
                array('addGSuiteUserToTeam', -1),
            ),
            TeamMembershipEvent::EDITED => array(
                array('createGSuiteUser', 1),
                array('addGSuiteUserToTeam', 0),
                array('removeGSuiteUserFromTeam', -1)
            ),
            TeamMembershipEvent::DELETED => array(
                array('removeGSuiteUserFromTeam', 0),
            ),
            TeamMembershipEvent::EXPIRED  => array(
                array('removeGSuiteUserFromTeam', 0)
            ),
            UserEvent::EDITED => array(
                array('updateGSuiteUser', 0),
            ),
            UserEvent::COMPANY_EMAIL_EDITED  => array(
                array('updateGSuiteUser', 0),
            ),
            TeamEvent::CREATED => array(
                array('createGSuiteTeam', 1),
                array('createGSuiteTeamDrive', -1),
            ),
            TeamEvent::EDITED => array(
                array('editGSuiteTeam', 0),
            )
        );
    }

    public function createGSuiteUser(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $companyEmail = $user->getCompanyEmail();

        if ($this->userExists($companyEmail)) {
            return;
        }

        if (!$companyEmail) {
            $emailsInUse = $this->googleAPI->getAllEmailsInUse();
            $this->emailMaker->setCompanyEmailFor($user, $emailsInUse);
        }

        if ($user->getCompanyEmail() !== null) {
            $this->userService->createUser($user);
            $this->logger->info("New G Suite account created for *{$user}* with email *{$user->getCompanyEmail()}*");
        }
    }

    public function addGSuiteUserToTeam(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $team = $event->getTeamMembership()->getTeam();
        $department = $user->getDepartment();

        $alreadyInGroup = $this->groupService->userIsInGroup($user, $team);

        if (!$alreadyInGroup && $user->getCompanyEmail()) {
            $this->groupService->addUserToGroup($user, $team);
            $this->logger->info("$user added to G Suite group *$department - $team*");
        }
    }

    public function removeGSuiteUserFromTeam(TeamMembershipEvent $event)
    {
        $user = $event->getTeamMembership()->getUser();
        $team = $event->getTeamMembership()->getTeam();
        $department = $user->getDepartment();

        $activeTeamMemberships = $user->getActiveTeamMemberships();
        $shouldBeInGroup = false;

        foreach ($activeTeamMemberships as $activeTeamMembership) {
            if ($team === $activeTeamMembership->getTeam()) {
                $shouldBeInGroup = true;
                break;
            }
            $shouldBeInGroup = false;
        }

        if (!$shouldBeInGroup && $user->getCompanyEmail()) {
            $this->groupService->removeUserFromGroup($user, $team);
            $this->logger->info("$user removed from G Suite group *$department - $team*");
        }
    }

    public function updateGSuiteUser(UserEvent $event)
    {
        $user = $event->getUser();
        $oldEmail = $event->getOldEmail();
        if ($this->userExists($oldEmail)) {
            $this->userService->updateUser($oldEmail, $user);
            $this->logger->info("G Suite account for *{$user}* with email *{$user->getCompanyEmail()}* has been updated.");
        }
    }

    public function createGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();

        if (!$this->teamExists($team)) {
            $this->groupService->createGroup($team);
            $this->logger->info("New G Suite group created for *$department - $team*");
        }
    }

    public function editGSuiteTeam(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();
        $oldEmail = $event->getOldTeamEmail();

        if (!$this->teamExists($oldEmail)) {
            $this->groupService->createGroup($team);
            $this->logger->info("New G Suite group created for *$department - $team*");
            $this->createGSuiteTeamDrive($event);
        } else {
            $this->groupService->updateGroup($oldEmail, $team);
            $this->logger->info("G Suite group for *$department - $team* has been updated");
        }
    }

    public function createGSuiteTeamDrive(TeamEvent $event)
    {
        $team = $event->getTeam();
        $department = $team->getDepartment();

        if (!$this->teamExists($team)) {
            $this->driveService->createTeamDrive($team);
            $this->logger->info("New Team Drive created for *$department - $team*");
        }
    }

    private function userExists($email)
    {
        if (!$email) {
            return false;
        }
        return $this->userService->getUser($email) !== null;
    }

    private function teamExists($email)
    {
        if (!$email) {
            return false;
        }
        return $this->groupService->getGroup($email) !== null;
    }
}
