<?php


namespace AppBundle\Service;

use AppBundle\Entity\UserGroupCollection;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use UnexpectedValueException;

class UserGroupCollectionManager
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UserGroupCollection $userGroupCollection
     */
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
            throw new InvalidArgumentException("Ugyldig antall grupper. Må være over eller lik 1.");
        } elseif ($groupSize<1) {
            throw new UnexpectedValueException("Ugyldig inndeling. Valgt inndeling ga ".sizeof($users)." bruker(e)");
        }

        $userGroupings = array_chunk($users, $groupSize);

        // Divide the remainder users over the first few groups
        $i = 0;
        while (sizeof($userGroupings)>$userGroupCollection->getNumberUserGroups()) {
            $userRemainderGroup = array_pop($userGroupings);
            foreach ($userRemainderGroup as $user) {
                $userGroupings[$i][] = $user;
                $i += 1;
            }
        }

        $this->em->persist($userGroupCollection);

        $groupName = 'A';
        foreach ($userGroupings as $userGrouping) {
            $userGroup = new UserGroup();
            $userGroup->setName($groupName);
            $userGroup->setUserGroupCollection($userGroupCollection);
            $groupName++;
            $userGroup->setUsers($userGrouping);
            $this->em->persist($userGroup);
        }
        $this->em->flush();
    }

    private function findUsers(UserGroupCollection $userGroupCollection)
    {
        $teamMembershipRepository = $this->em->getRepository('AppBundle:TeamMembership');

        $teamMemberships = array();
        foreach ($userGroupCollection->getTeams() as $team) {
            $teamMemberships = array_merge($teamMemberships, $teamMembershipRepository->findByTeam($team));
        }

        $teamMembershipsFilteredBySemesters = array();
        foreach ($userGroupCollection->getSemesters() as $semester) {
            $teamMembershipsFilteredBySemesters = array_merge(
                $teamMembershipsFilteredBySemesters,
                $teamMembershipRepository->filterNotInSemester($teamMemberships, $semester)
            );
        }

        $teamUsersFiltered = array_map(
            function (TeamMembership $teammembership) {
                return $teammembership->getUser();
            },
            $teamMembershipsFilteredBySemesters
        );


        $assistantHistoryRepository = $this->em->getRepository('AppBundle:AssistantHistory');
        $assistantHistories = array();
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
            function (AssistantHistory $assistantHistory) use ($bolks) {
                return in_array($assistantHistory->getBolk(), $bolks);
            }
        );

        $assistantsFiltered= array_map(
            function (AssistantHistory $assistantHistory) {
                return $assistantHistory->getUser();
            },
            $assistantHistories
        );


        $users = array_merge($teamUsersFiltered, $assistantsFiltered);

        $selectedUsers = $userGroupCollection->getUsers()->toArray();
        $users = array_merge($users, $selectedUsers);


        $usersUnique = array_unique($users, SORT_REGULAR);

        return $usersUnique;
    }
}
