<?php
namespace AppBundle\SchoolAllocation;

class Allocation
{
    private $schools;//Array with School objects
    private $assistants;//Array with Assistant objects

    /**
     * Allocation constructor.
     * @param School[] schools
     * @param Assistant[] assistants
     */
    public function __construct($schools, $assistants)
    {
        $this->assistants = $assistants;
        $this->schools = $schools;
    }


    public function step()
    {
        //TODO: Check if all assistants has been allocated or all schools has filled up their capacity and return solution if it is optimal or return null
        foreach ($this->assistants as $assistant) {
            if ($assistant->isAssignedToSchool()) break;//Assistant has been assigned a school
            $availability = $assistant->getAvailability();
            arsort($availability);
            foreach ($availability as $day => $isAvailable) {
                if (!$isAvailable) break;//When there is no schools with capacity on available day
                foreach ($this->schools as $school) {
                    if ($assistant->isDoublePosition()) {
                        if($school->getCapacity()[1][$day] > 0 && $school->getCapacity()[2][$day] > 0){//There is capacity left in both group 1 and group 2
                            $assistantsCopy = $this->copyAssistants();
                            $schoolsCopy = $this->copySchools();
                            $allocationCopy = new Allocation($assistantsCopy, $schoolsCopy);
                            $allocationCopy->assignAssistantToSchool($assistant, $school, 1, $day);
                            $allocationCopy->assignAssistantToSchool($assistant, $school, 2, $day);
                            $allocationCopySolution = $allocationCopy->step();
                            if(!is_null($allocationCopySolution)) return $allocationCopySolution;
                            break;
                        }
                    } else {

                        for ($group = 1; $group <= 2; $group++) {
                            if ($group == 1 && $assistant->getPreferredGroup() == 2 || $group == 2 && $assistant->getPreferredGroup() == 1) continue;//Don't assign assistant to other than the preferred group
                            $capacity = $school->getCapacity()[$group];

                            if ($capacity[$day] > 0) {
                                $assistantsCopy = $this->copyAssistants();
                                $schoolsCopy = $this->copySchools();
                                $allocationCopy = new Allocation($assistantsCopy, $schoolsCopy);
                                $allocationCopy->assignAssistantToSchool($assistant, $school, $group, $day);
                                $allocationCopySolution = $allocationCopy->step();
                                if(!is_null($allocationCopySolution)) return $allocationCopySolution;
                                break;
                            }
                        }

                    }
                    if ($assistant->isAssignedToSchool()) break;//Assistant has been assigned a school
                }
                if ($assistant->isAssignedToSchool()) break;//Assistant has been assigned a school
            }
        }
        return null;//No solution found
    }

    /**
     * @param Assistant $assistant
     * @param School $school
     * @param int $group
     * @param string $day
     */
    private function assignAssistantToSchool($assistant, $school, $group, $day)
    {
        $assistant->assignToSchool($school, $group, $day);
        $school->addAssistant($group, $day);
    }

    /**
     * TODO: Implement this
     * @return Assistant[]
     */
    private function copyAssistants()
    {
        return $this->assistants;
    }

    /**
     * TODO: Implement this
     * @return School[]
     */
    private function copySchools()
    {
        return $this->schools;
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