<?php

namespace App\Organization\Infrastructure;

use App\Operations\Infrastructure\Entity\AssistantHistory;
use App\Organization\Infrastructure\Entity\TeamMembership;
use App\Organization\Infrastructure\Entity\UserGroup;
use App\Organization\Infrastructure\Entity\UserGroupCollection;
use Doctrine\ORM\EntityManagerInterface;

class UserGroupCollectionManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function initializeUserGroupCollection(UserGroupCollection $userGroupCollection)
    {
        if (!empty($userGroupCollection->getUserGroups())) {
            foreach ($userGroupCollection->getUserGroups() as $userGroup) {
                $this->em->remove($userGroup);
            }
        }
        $users = $this->findUsers($userGroupCollection);
        $userGroupCollection->setNumberTotalUsers(sizeof($users));
        shuffle($users);
        $groupSize = intdiv(sizeof($users), $userGroupCollection->getNumberUserGroups());
        if ($userGroupCollection->getNumberUserGroups() < 1) {
            throw new \InvalidArgumentException('Ugyldig antall grupper. Må være over eller lik 1.');
        } elseif ($groupSize < 1) {
            throw new \UnexpectedValueException('Ugyldig inndeling. Valgt inndeling ga '.sizeof($users).' bruker(e)');
        }

        $userGroupings = array_chunk($users, $groupSize);

        // Divide the remainder users over the first few groups
        $i = 0;
        while (sizeof($userGroupings) > $userGroupCollection->getNumberUserGroups()) {
            $userRemainderGroup = array_pop($userGroupings);
            foreach ($userRemainderGroup as $user) {
                $userGroupings[$i][] = $user;
                ++$i;
            }
        }

        $this->em->persist($userGroupCollection);

        $groupName = 'A';
        foreach ($userGroupings as $userGrouping) {
            $userGroup = new UserGroup();
            $userGroup->setName($groupName);
            $userGroup->setUserGroupCollection($userGroupCollection);
            ++$groupName;
            $userGroup->setUsers($userGrouping);
            $this->em->persist($userGroup);
        }
        $this->em->flush();
    }

    private function findUsers(UserGroupCollection $userGroupCollection)
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        $teamMemberships = [];
        foreach ($userGroupCollection->getTeams() as $team) {
            $teamMemberships = array_merge($teamMemberships, $teamMembershipRepository->findByTeam($team));
        }

        $teamMembershipsFilteredBySemesters = [];
        foreach ($userGroupCollection->getSemesters() as $semester) {
            $teamMembershipsFilteredBySemesters = array_merge(
                $teamMembershipsFilteredBySemesters,
                $teamMembershipRepository->filterNotInSemester($teamMemberships, $semester)
            );
        }

        $teamUsersFiltered = array_map(
            fn (TeamMembership $teammembership) => $teammembership->getUser(),
            $teamMembershipsFilteredBySemesters
        );

        $assistantHistoryRepository = $this->em->getRepository(AssistantHistory::class);
        $assistantHistories = [];
        foreach ($userGroupCollection->getAssistantsDepartments() as $department) {
            foreach ($userGroupCollection->getSemesters() as $semester) {
                $assistantHistories = array_merge(
                    $assistantHistories,
                    $assistantHistoryRepository->findByDepartmentAndSemester($department, $semester)
                );
            }
        }
        $bolks = $userGroupCollection->getAssistantBolks();

        $assistantHistories = array_filter(
            $assistantHistories,
            fn (AssistantHistory $assistantHistory) => in_array($assistantHistory->getBolk(), $bolks)
        );

        $assistantsFiltered = array_map(
            fn (AssistantHistory $assistantHistory) => $assistantHistory->getUser(),
            $assistantHistories
        );

        $users = array_merge($teamUsersFiltered, $assistantsFiltered);

        $selectedUsers = $userGroupCollection->getUsers()->toArray();
        $users = array_merge($users, $selectedUsers);

        return array_unique($users, SORT_REGULAR);
    }
}
