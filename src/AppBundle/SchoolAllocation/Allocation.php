<?php

namespace AppBundle\SchoolAllocation;

class Allocation
{
    private $schools; //Array with School objects
    private $assistants; //Array with Assistant objects
    public static $count;

    /**
     * Allocation constructor.
     *
     * @param School[] schools
     * @param Assistant[] assistants
     */
    public function __construct($schools, $assistants)
    {
        $this->assistants = $assistants;
        $this->schools = $schools;
    }

    /**
     * @return $this|void
     */
    public function step()
    {
        if (self::$count == 0) {
            self::$count = 1;
        } else {
            ++self::$count;
        }

        if ($this->isOptimalSolution()) {
            return $this;
        }

        foreach ($this->assistants as $assistant) {
            if ($assistant->isAssignedToSchool()) {
                continue;
            }//Assistant has been assigned a school
            $availability = $assistant->getAvailability();
            arsort($availability);
            foreach ($availability as $day => $isAvailable) {
                if (!$isAvailable) {
                    break;
                }//When there is no schools with capacity on available day
                foreach ($this->schools as $school) {
                    if ($assistant->isDoublePosition()) {
                        if ($school->getCapacity()[1][$day] > 0 && $school->getCapacity()[2][$day] > 0) {
                            //There is capacity left in both group 1 and group 2
                            $assistantsCopy = $this->copyAssistants();
                            $schoolsCopy = $this->copySchools();
                            $allocationCopy = new self($schoolsCopy, $assistantsCopy);
                            $allocationCopy->assignAssistantToSchool($allocationCopy->findAssistantByName($assistant->getName()), $allocationCopy->findSchoolByName($school->getName()), 1, $day);
                            $allocationCopy->assignAssistantToSchool($allocationCopy->findAssistantByName($assistant->getName()), $allocationCopy->findSchoolByName($school->getName()), 2, $day);
                            $allocationCopySolution = $allocationCopy->step();
                            if (!is_null($allocationCopySolution)) {
                                return $allocationCopySolution;
                            }
                        }
                    } else {
                        for ($group = 1; $group <= 2; ++$group) {
                            if ($group == 1 && $assistant->getPreferredGroup() == 2 || $group == 2 && $assistant->getPreferredGroup() == 1) {
                                continue;
                            }//Don't assign assistant to other than the preferred group
                            $capacity = $school->getCapacity()[$group];

                            if ($capacity[$day] > 0) {
                                $assistantsCopy = $this->copyAssistants();
                                $schoolsCopy = $this->copySchools();
                                $allocationCopy = new self($schoolsCopy, $assistantsCopy);
                                $allocationCopy->assignAssistantToSchool($allocationCopy->findAssistantByName($assistant->getName()), $allocationCopy->findSchoolByName($school->getName()), $group, $day);
                                $allocationCopySolution = $allocationCopy->step();
                                if (!is_null($allocationCopySolution)) {
                                    return $allocationCopySolution;
                                }
                            }
                        }
                    }
                }
            }
        }

        return; //No solution found
    }

    /**
     * @return bool
     */
    private function isOptimalSolution()
    {
        return $this->schoolsFull() || $this->allAssistantsAssigned();
    }

    /**
     * @return bool
     */
    private function schoolsFull()
    {
        foreach ($this->schools as $school) {
            if (!$school->isFull()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function allAssistantsAssigned()
    {
        foreach ($this->assistants as $assistant) {
            if (!$assistant->isAssignedToSchool()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Assistant $assistant
     * @param School    $school
     * @param int       $group
     * @param string    $day
     */
    public function assignAssistantToSchool($assistant, $school, $group, $day)
    {
        $assistant->assignToSchool($school, $group, $day);
        $school->addAssistant($group, $day);
    }

    /**
     * @return Assistant[]
     */
    private function copyAssistants()
    {
        $assistantsCopy = array();
        foreach ($this->assistants as $assistant) {
            $assistantsCopy[] = clone $assistant;
        }

        return $assistantsCopy;
    }

    /**
     * @return School[]
     */
    private function copySchools()
    {
        $schoolsCopy = array();
        foreach ($this->schools as $school) {
            $schoolsCopy[] = clone $school;
        }

        return $schoolsCopy;
    }

    /**
     * @param string $name
     *
     * @return Assistant|null
     */
    public function findAssistantByName($name)
    {
        foreach ($this->assistants as $assistant) {
            if ($assistant->getName() == $name) {
                return $assistant;
            }
        }

        return;
    }

    /**
     * @param string $name
     *
     * @return School|null
     */
    public function findSchoolByName($name)
    {
        foreach ($this->schools as $school) {
            if ($school->getName() == $name) {
                return $school;
            }
        }

        return;
    }

    /**
     * @return School[]
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * @param School[] $schools
     */
    public function setSchools($schools)
    {
        $this->schools = $schools;
    }

    /**
     * @return Assistant[]
     */
    public function getAssistants()
    {
        return $this->assistants;
    }

    /**
     * @param Assistant[] $assistants
     */
    public function setAssistants($assistants)
    {
        $this->assistants = $assistants;
    }
}
