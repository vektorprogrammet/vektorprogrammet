<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\ExecutiveBoardMember;
use AppBundle\Entity\Position;
use AppBundle\Entity\WorkHistory;
use AppBundle\Twig\Extension\GroupSortExtension;

class GroupSortExtensionUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testExecutiveMembers()
    {
        $members = array();
        $positions = ['Medlem', 'Leder', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        for ($x = 0; $x < 6; ++$x) {
            $member = new ExecutiveBoardMember();
            $member->setPosition($positions[$x]);
            $members[] = $member;
        }

        $groupSorter = new GroupSortExtension();

        $sortedMembers = $groupSorter->groupSortFilter($members);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Medlem', 'Økonomi'];
        for ($x = 0; $x < 6; ++$x) {
            $this->assertEquals($sortedMembers[$x]->getPositionName(), $sortedPositions[$x]);
        }
    }

    public function testWorkHistories()
    {
        $members = array();
        $positions = ['Sekretær', 'Medlem', 'Leder', 'Økonomi', 'Assistent', 'Medlem', 'Nestleder'];
        for ($x = 0; $x < 7; ++$x) {
            $member = new WorkHistory();
            $position = new Position();
            $position->setName($positions[$x]);
            $member->setPosition($position);
            $members[] = $member;
        }

        $groupSorter = new GroupSortExtension();

        $sortedMembers = $groupSorter->groupSortFilter($members);
        $sortedPositions = ['Leder', 'Nestleder', 'Assistent', 'Medlem', 'Medlem', 'Sekretær', 'Økonomi'];
        for ($x = 0; $x < 7; ++$x) {
            $this->assertEquals($sortedMembers[$x]->getPositionName(), $sortedPositions[$x]);
        }
    }
}
