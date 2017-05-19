<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\GroupMemberInterface;

class GroupSortExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('groupSort', array($this, 'groupSortFilter')),
        );
    }

    /**
     * Takes a list of members from a group. A group is an ExecutiveBoard or
     * a team.
     * Returns a sorted list with "Leder" and "Nestleder" in the first and second
     * position, respectively.
     *
     * @param GroupMemberInterface[] $group
     *
     * @return GroupMemberInterface[]
     */
    public function groupSortFilter($group): array
    {
        $leaders = array();
        $members = array();

        foreach ($group as $member) {
            $position = strtolower($member->getPositionName());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $member;
            } else {
                $members[] = $member;
            }
        }

        usort($members, array($this, 'positionsCompare'));
        usort($leaders, array($this, 'positionsCompare'));

        return array_merge($leaders, $members);
    }

    private function positionsCompare(GroupMemberInterface $a, GroupMemberInterface $b)
    {
        return strcmp(strtolower($a->getPositionName()), strtolower($b->getPositionName()));
    }
}
