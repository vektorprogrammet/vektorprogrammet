<?php

namespace App\Service;

use App\Models\Semester;
use App\Models\TeamMembership;
use App\Event\TeamMembershipEvent;
use App\Repository\Contract\SemesterRepositoryInterface;
use App\Repository\Contract\TeamMembershipRepositoryInterface;
use App\Service\Contract\TeamMembershipServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TeamMembershipService implements TeamMembershipServiceInterface
{
    private TeamMembershipRepositoryInterface $teamMembershipRepository;
    private SemesterRepositoryInterface $semesterRepository;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        TeamMembershipRepositoryInterface $teamMembershipRepository,
        SemesterRepositoryInterface $semesterRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->semesterRepository = $semesterRepository;
        $this->dispatcher = $dispatcher;
    }

    public function updateTeamMemberships()
    {
        $activeMemberships = $this->teamMembershipRepository->findActiveTeamMemberships();
        $currentSemester = $this->semesterRepository->findOrCreateCurrentSemester();
        $currentSemesterStartDate = $currentSemester->start_date;

        foreach ($activeMemberships as $teamMembership) {
            $endSemester = $teamMembership->endSemester ?? null;
            if ($endSemester) {
                if ($endSemester->end_date <= $currentSemesterStartDate) {
                    $teamMembership->is_suspended = true;
                    $teamMembership->save();
                    $this->dispatcher->dispatch(TeamMembershipEvent::EXPIRED, new TeamMembershipEvent($teamMembership));
                }
            }
        }

        return $activeMemberships;
    }
}
