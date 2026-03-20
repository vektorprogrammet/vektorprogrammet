<?php

namespace App\Organization\Domain\Rules;

class UserGroupDistribution
{
    /**
     * Distribute users into N equal-sized groups with remainder spread across first groups.
     *
     * @param array $users      List of users to distribute (will be shuffled)
     * @param int   $groupCount Number of groups to create (must be >= 1)
     *
     * @return array[] Array of arrays, each containing users for one group
     *
     * @throws \InvalidArgumentException If groupCount < 1
     * @throws \UnexpectedValueException If too few users for the requested groups
     */
    public function distribute(array $users, int $groupCount): array
    {
        if ($groupCount < 1) {
            throw new \InvalidArgumentException('Ugyldig antall grupper. Må være over eller lik 1.');
        }

        shuffle($users);

        $groupSize = intdiv(count($users), $groupCount);
        if ($groupSize < 1) {
            throw new \UnexpectedValueException('Ugyldig inndeling. Valgt inndeling ga '.count($users).' bruker(e)');
        }

        $groups = array_chunk($users, $groupSize);

        // Distribute remainder users across the first groups
        $i = 0;
        while (count($groups) > $groupCount) {
            $remainderGroup = array_pop($groups);
            foreach ($remainderGroup as $user) {
                $groups[$i][] = $user;
                ++$i;
            }
        }

        return $groups;
    }
}
