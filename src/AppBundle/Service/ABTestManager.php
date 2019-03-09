<?php


namespace AppBundle\Service;

use AppBundle\Entity\ABTest;
use AppBundle\Entity\AssistantHistory;
use AppBundle\Entity\TeamMembership;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use AppBundle\Role\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class ABTestManager
{
    private $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param ABTest $ABTest
     * @return ABTest
     */
    public function initializeABTest(ABTest $ABTest)
    {
        $users = $this->findUsers($ABTest);
        shuffle($users);
        $groupSize = intdiv(sizeof($users), $ABTest->getNumberUserGroups());
        if ($ABTest->getNumberUserGroups() < 1){
            throw new \InvalidArgumentException("Ugyldig antall grupper. Må være over 1.");
        }
        elseif($groupSize<1)
        {
            throw new \UnexpectedValueException("For få brukere til slik inndeling. Valgt inndeling ga ".sizeof($users)." brukere");
        }

        $userGroupings = array_chunk($users, $groupSize);

        // Divide the remainder users over the first few groups
        $i = 0;
        while(sizeof($userGroupings)>$ABTest->getNumberUserGroups())
        {
            $userRemainderGroup = array_pop($userGroupings);
            foreach($userRemainderGroup as $user)
            {
                $userGroupings[$i][] = $user;
                $i += 1;
            }
        }

        $userGroups = array();
        $groupName = 'A';
        foreach($userGroupings as $userGrouping)
        {
            $userGroup = new UserGroup();
            $userGroup->setName($groupName);
            $groupName++;
            $this->em->persist($userGroup);
            $userGroup->setUsers($userGrouping);
            $userGroups[] = $userGroup;
        }
        $this->em->flush();

        $ABTest->setUserGroups($userGroups);

        return $ABTest;

    }

    private function findUsers(ABTest $ABTest){
        $teamMembershipRepository = $this->em->getRepository('AppBundle:TeamMembership');

        $teamMemberships = array();
        foreach ($ABTest->getTeams() as $team)
        {
            $teamMemberships = array_merge($teamMemberships, $teamMembershipRepository->findByTeam($team));
        }

        $teamMembershipsFilteredBySemesters = array();
        foreach ($ABTest->getSemesters() as $semester)
        {
            $teamMembershipsFilteredBySemesters = array_merge($teamMembershipsFilteredBySemesters,
                $teamMembershipRepository->filterNotInSemester($teamMemberships, $semester));
        }

        $teamUsersFiltered = array_map(
            function (TeamMembership $teammembership){return $teammembership->getUser();},
            $teamMembershipsFilteredBySemesters);


        $assistantHistoryRepository = $this->em->getRepository('AppBundle:AssistantHistory');
        $assistantHistories = array();
        foreach ($ABTest->getAssistantsDepartments() as $department){
            foreach ($ABTest->getSemesters() as $semester){
                $assistantHistories = array_merge($assistantHistories,
                    $assistantHistoryRepository->findByDepartmentAndSemester($department, $semester));
            }
        }
        $bolks = $ABTest->getAssistantBolks();

        $assistantHistories = array_filter(
            $assistantHistories,
            function (AssistantHistory $assistantHistory) use ($bolks){ return in_array($assistantHistory->getBolk(),$bolks);});

        $assistantsFiltered= array_map(
            function (AssistantHistory $assistantHistory){return $assistantHistory->getUser();},
            $assistantHistories);


        $users = array_merge($teamUsersFiltered, $assistantsFiltered);
        $usersUnique = array_unique($users, SORT_REGULAR);

        return $usersUnique;
    }




}
