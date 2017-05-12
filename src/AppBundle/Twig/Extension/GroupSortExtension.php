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
            $position = strtolower($member->getPosition());
            if ($position === 'leder' || $position === 'nestleder') {
                $leaders[] = $member;
            } else {
                $members[] = $member;
            }
        }

        usort($members, function (GroupMemberInterface $a, GroupMemberInterface $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        usort($leaders, function (GroupMemberInterface $a, GroupMemberInterface $b) {
            return strcmp(strtolower($a->getPosition()), strtolower($b->getPosition()));
        });

        return array_merge($leaders, $members);
    }
}
