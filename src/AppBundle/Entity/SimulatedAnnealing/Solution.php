<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Solution
{
    private $schools;//Array with School objects
    private $assistants;//Array with Assistant objects
    private $lockedAssistants;
    public $initializeTime;
    public $improveTime;
    public $optimizeTime;
    private $toBeImproved;
    public static $visited;

    /**
     * Solution constructor.
     * @param mixed schools
     * @param mixed assistants
     */
    public function __construct($schools, $assistants)
    {
        $this->initializeTime = -1;
        $this->optimizeTime = -1;
        $this->assistants = $assistants;
        $this->toBeImproved = array();
        $this->lockedAssistants = array();
        $this->schools = $schools;
    }

    public function initializeSolution($optimize = false, $sort = false)
    {
        $startTime = round(microtime(true) * 1000);
        if (!$optimize) {
            foreach ($this->assistants as $assistant) {

                //Sort availability lists, best day first.
                $availabilitySorted = $assistant->getAvailability();
                //arsort($availabilitySorted);
                $assistant->setAvailability($availabilitySorted);
                $assigned = false;
                foreach ($this->schools as $school) {
                    foreach ($school->getCapacity() as $day => $cap) {
                        if ($this->capacityLeftOnDay($day, $school) > 0) {
                            $assistant->setAssignedSchool($school->getName());
                            $assistant->setAssignedDay($day);
                            $this->addAssistantToSchool($school, $day);
                            //$this->assistants[] = $assistant;
                            if ($assistant->getAvailability()[$day] !== 2) {
                                $this->toBeImproved[] = $assistant;
                            }
                            $assigned = true;
                            break;
                        }
                        if ($assigned) break;
                    }
                    if ($assigned) break;
                }
            }
        } else {
            foreach ($this->assistants as $assistant) {
                //Sort availability lists, best day first.
                $availabilitySorted = $assistant->getAvailability();
                if ($sort) {
                    arsort($availabilitySorted);
                }
                $assistant->setAvailability($availabilitySorted);

                $i = 0;
                $bestSchool = null;
                $bestDayCapacity = 0;
                $bestDay = null;

                while ($bestSchool === null) {

                    if ($i > 4) break; //If there is no capacity left in any school

                    $bestDay = array_keys($availabilitySorted)[$i];

                    //TODO: Sort schools by #Assistants. Lowest first
                    //TODO: Select first school with number of assistants on bestDay < 2
                    //TODO: If every school has at least 2 assistant on bestDay select school that has highest capacity left on bestDay
                    foreach ($this->schools as $school) {
                        if ($school->getCapacity()[$bestDay] == 0) {
                            $capacityOnBestDay = 0;
                        } else {
                            $capacityOnBestDay = $this->capacityLeftOnDay($bestDay, $school) / $school->getCapacity()[$bestDay];
                        }
                        //If no bestSchool has been set yet and there is capacity on this school
                        if ($bestSchool === null && $capacityOnBestDay > 0) {
                            $bestSchool = $school;
                            $bestDayCapacity = $capacityOnBestDay;
                            continue;
                        } elseif ($bestSchool !== null) {
                            //Find the best school. The best school will be the school with most capacity left on the weekday that is best for the assistant.
                            if ($capacityOnBestDay > $bestDayCapacity) {
                                $bestDayCapacity = $capacityOnBestDay;
                                $bestSchool = $school;
                            }
                        }
                    }
                    $i++;
                }
                if ($i > 5) break;//If there is no capacity left in any school

                //Update the assistant with the bestSchool and bestDay found and add it to the assistants list
                $assistant->setAssignedSchool($bestSchool->getName());
                $assistant->setAssignedDay($bestDay);
                $this->addAssistantToSchool($bestSchool, $bestDay);
                //$this->assistants[] = $assistant;
                if ($assistant->getAvailability()[$bestDay] !== 2) {
                    $this->toBeImproved[] = $assistant;
                }
            }
        }
        $this->initializeTime = (round(microtime(true) * 1000) - $startTime) / 1000;
        Solution::$visited = 1;
        //Solution::$visited[] = $this;

    }

    public
    function improveSolution()
    {
        $startTime = round(microtime(true) * 1000);
        $changed = true;
        while ($changed && sizeof($this->toBeImproved) > 0) {

            $changed = false;
            foreach ($this->toBeImproved as $assistant) {

                $availabilitySorted = $assistant->getAvailability();
                $currentDay = $assistant->getAssignedDay();
                $currentScore = $availabilitySorted[$currentDay];

                foreach ($this->schools as $school) {
                    foreach ($availabilitySorted as $bestDay => $score) {
                        if ($score == 0) break;
                        if ($score <= $currentScore) break;
                        if (!array_key_exists($bestDay, $school->getAssistants())) continue;
                        foreach ($this->getAllAssistantOnSchoolByDay($school, $bestDay) as $assistantInSchool) {

                            if ($assistantInSchool->getAvailability()[$currentDay] >= $assistantInSchool->getAvailability()[$assistantInSchool->getAssignedDay()]) {
                                $this->swampAssistants($assistantInSchool, $assistant);
                                if ($score == 2) $this->removeFromImprove($assistant);
                                $changed = true;
                                break;
                            }
                        }
                        if ($changed) break;
                    }
                    if ($changed) break;
                }
                if ($changed) break;
            }
        }
        $this->improveTime = (round(microtime(true) * 1000) - $startTime) / 1000;
    }

    /**
     * @return int|int in [0,100]
     */
    public
    function evaluate($log = false)
    {
        //TODO: Adjust points to achieve wanted results
        if (sizeof($this->assistants) === 0) {
            return 0;
        }
        $maxPoints = 0;
        $points = 0;
        foreach ($this->assistants as $assistant) {
            $avP = $assistant->getAvailability()[$assistant->getAssignedDay()];
            if ($avP == 2) {
                $points += 100;
            } elseif ($avP == 1) {
                $points += 80;
            } else {
                $points += 0;
            }
            $maxPoints += 100;
        }
        foreach ($this->schools as $school) {
            $maxPoints += 200;
            if ($school->hasAssistants()) {
                $points += 200;
            }

            foreach ($school->getAssistants() as $day => $amount) {
                if ($log) {
                    dump("School " . $school->getName() . " has " . $amount . " assistants on " . $day);
                    dump("Points before eval: " . (String)(100 * $points / $maxPoints));
                }
                if ($amount > 0) {
                    $maxPoints += 200;
                    if ($amount >= 2) {
                        $points += 200;
                    }
                }
                if ($log) {
                    dump("Points after eval: " . (String)(100 * $points / $maxPoints));
                }
            }
        }
        if ($maxPoints == 0) return 0;
        return 100 * $points / $maxPoints;
    }

    public
    function generateNeighbours()
    {
        $neighbours = array();
        $assistantIndex = 0;
        foreach ($this->assistants as $assistant) {
            $schoolIndex = 0;
            foreach ($this->getSchools() as $school) {
                foreach ($school->getCapacity() as $day => $capacity) {
                    if ($capacity === 0) continue;
                    //Check if the school has capacity for more assistants
                    $freeCapacity = $this->capacityLeftOnDay($day, $school);
                    if ($freeCapacity < 1) continue;
                    if($school->totalAssistants() > $this->getSchoolByName($assistant->getAssignedSchool())->totalAssistants() ) continue;

                    //Create a deep copy of the current solution
                    $schoolsCopy = $this->deepCopySchools();
                    $assistantsCopy = $this->deepCopyAssistants();
                    $newSolution = new Solution($schoolsCopy, $assistantsCopy);
                    //Move the assistant from current school to a new school and add the new solution to the neighbour list
                    $newSolution->moveAssistant($assistantsCopy[$assistantIndex], $schoolsCopy[$schoolIndex]->getName(), $assistant->getAssignedDay(), $day);
                    /*if(sizeof(Solution::$visited) > 0 && in_array($newSolution, Solution::$visited)){
                        continue;
                    }*/
                    Solution::$visited++;
                    $neighbours[] = $newSolution;
                }
                $schoolIndex++;
            }
            $assistantIndex++;
        }
        return $neighbours;
    }

    public
    function capacityLeftOnDay($day, $school)
    {
        $assistants = $school->getAssistants();
        return array_key_exists($day, $assistants) ? $school->getCapacity()[$day] - $assistants[$day] : $school->getCapacity()[$day];
    }

    public
    function addAssistantToSchool($school, $day)
    {
        $school->addAssistant($day);
    }

    public function sortSchoolsByNumberOfAssistants($day){
        $sortedSchools = array();
        $tempSorted = array();
        foreach ($this->schools as $school) {
            $index = $school->totalAssistants();
            //$index = $this->capacityLeftOnDay($day, $school);
            if(!array_key_exists($index, $tempSorted))$tempSorted[$index] = array();
            $tempSorted[$index][] = $school;
        }
        ksort($tempSorted);
        dump($tempSorted);

    }

    private
    function getAllAssistantOnSchoolByDay(School $school, $day)
    {
        $assistants = array();
        $schoolName = $school->getName();
        foreach ($this->assistants as $assistant) {
            if ($assistant->getAssignedSchool() === $schoolName && $assistant->getAssignedDay() === $day) {
                $assistants[] = $assistant;
            }
        }
        return $assistants;
    }

    public
    function swampAssistants(Assistant $a, Assistant $b)
    {
        $aSchool = $a->getAssignedSchool();
        $aDay = $a->getAssignedDay();
        $bSchool = $b->getAssignedSchool();
        $bDay = $b->getAssignedDay();

        $this->moveAssistant($a, $bSchool, $aDay, $bDay);
        $this->moveAssistant($b, $aSchool, $bDay, $aDay);

    }

    public
    function moveAssistant(Assistant $assistant, $schoolName, $fromDay, $toDay)
    {
        $school = $this->getSchoolByName($assistant->getAssignedSchool());
        $newSchool = $this->getSchoolByName($schoolName);
        $school->removeAssistant($fromDay);
        $assistant->setAssignedSchool($schoolName);
        $assistant->setAssignedDay($toDay);
        $this->addAssistantToSchool($newSchool, $toDay);
    }

    private
    function getSchoolByName($schoolName)
    {
        foreach ($this->schools as $school) {
            if ($school->getName() === $schoolName) {
                return $school;
            }
        }
    }

    public
    function addSchool($school)
    {
        $this->schools[] = $school;
    }

    private
    function removeFromImprove(Assistant $assistant)
    {
        unset($this->toBeImproved[array_search($assistant, $this->toBeImproved)]);
    }

    public
    function deepCopySchools()
    {
        $copy = array();
        foreach ($this->schools as $school) {
            $copy[] = clone $school;
        }

        return $copy;
    }

    public
    function deepCopy()
    {
        $schoolsCopy = $this->deepCopySchools();
        $assistantsCopy = $this->deepCopyAssistants();
        $newSolution = new Solution($schoolsCopy, $assistantsCopy);
        return $newSolution;
    }

    public function deepCopyAssistants()
    {
        $copy = array();
        foreach ($this->assistants as $assistant) {
            $copy[] = clone $assistant;
        }
        return $copy;
    }

    public
    function replaceSchool($oldSchool, $newSchool)
    {
        $this->schools[array_search($oldSchool, $this->schools)] = $newSchool;
    }

    public
    function removeSchool($school)
    {
        unset($this->schools[array_search($school, $this->schools)]);
    }

    public
    function addAssistant($assistant)
    {
        $this->assistants[] = $assistant;
    }

    public
    function removeAssistant($assistant)
    {
        unset($this->assistants[array_search($assistant, $this->assistants)]);
    }

    /**
     * @return mixed
     */
    public
    function getSchools()
    {
        return $this->schools;
    }

    /**
     * @param mixed $schools
     */
    public
    function setSchools($schools)
    {
        $this->schools = $schools;
    }

    /**
     * @return array
     */
    public
    function getLockedAssistants()
    {
        return $this->lockedAssistants;
    }

    /**
     * @param array $lockedAssistants
     */
    public
    function setLockedAssistants($lockedAssistants)
    {
        $this->lockedAssistants = $lockedAssistants;
    }

    /**
     * @return mixed
     */
    public
    function getAssistants()
    {
        return $this->assistants;
    }

    /**
     * @param mixed $assistants
     */
    public
    function setAssistants($assistants)
    {
        $this->assistants = $assistants;
    }


}