<?php

namespace AppBundle\Google;

use AppBundle\Entity\Team;
use Ramsey\Uuid\Uuid;

class GoogleDrive extends GoogleService
{
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
        $permission->setRole('fileOrganizer');
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
}
