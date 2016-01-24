<?php
namespace AppBundle\SchoolAllocation;

class Allocation
{
    private $schools;//Array with School objects
    private $assistants;//Array with Assistant objects
    private $lockedAssistants;
    public $initializeTime;
    public $improveTime;
    public $optimizeTime;
    private $toBeImproved;
    public static $visited;
    public static $numSwaps;

    /**
     * Allocation constructor.
     * @param School[] schools
     * @param Assistant[] assistants
     * @param boolean isAnewAllocation
     * @param Assistant[] lockedAssistants
     */
    public function __construct($schools, $assistants, $isAnewAllocation = false, $lockedAssistants = array())
    {
        $this->initializeTime = 0;
        $this->optimizeTime = 0;
        $this->assistants = $assistants;
        $this->toBeImproved = array();
        $this->lockedAssistants = $lockedAssistants;
        $this->schools = $schools;
        if($isAnewAllocation){
            $this->initializeAllocation();
            $this->improveAllocation();
        }
    }

    public function initializeAllocation()
    {
        //Assign assistants with double position
        foreach($this->assistants as $assistant){
            if($lockedAssistant = $this->isInLockedList($assistant)){
                $school = $this->getSchoolByName($lockedAssistant->getAssignedSchool());
                $this->addAssistantToSchool($school, $lockedAssistant->getAssignedDay());
                $assistant->setAssignedDay($lockedAssistant->getAssignedDay());
                $assistant->setAssignedSchool($lockedAssistant->getAssignedSchool());
            }
        }

        $startTime = round(microtime(true) * 1000);
        foreach ($this->assistants as $assistant) {
            if($assistant->getAssignedSchool() !== null)continue;
            if($this->isInLockedList($assistant) !== null){
                continue;
            }
            //Sort availability lists, best day first.
            $availabilitySorted = $assistant->getAvailability();
            arsort($availabilitySorted);
            $assistant->setAvailability($availabilitySorted);

            $i = 0;
            $bestSchool = null;
            $bestDay = null;

            while ($bestSchool === null) {

                if ($i > 4) break; //If there is no capacity left in any school

                $bestDay = array_keys($availabilitySorted)[$i];
                $schools = $this->sortSchoolsByNumberOfAssistants($bestDay);
                foreach ($schools as $school) {
                    if ($this->capacityLeftOnDay($bestDay, $school) > 0) {
                        $bestSchool = $school;
                        break;
                    }
                }
                $i++;
            }
            if ($i > 4) break;//If there is no capacity left in any school

            //Update the assistant with the bestSchool and bestDay found and add it to the assistants list
            $assistant->setAssignedSchool($bestSchool->getName());
            $assistant->setAssignedDay($bestDay);
            $this->addAssistantToSchool($bestSchool, $bestDay);
        }

        $this->initializeTime = (round(microtime(true) * 1000) - $startTime) / 1000;
        Allocation::$visited = 1;

    }

    public function improveAllocation()
    {
        $startTime = round(microtime(true) * 1000);

        //Try to swap school with other assistants
        $changed = true;
        while ($changed) {

            $changed = false;
            foreach ($this->assistants as $assistant) {
                if($this->isInLockedList($assistant) !== null)continue;
                $availabilitySorted = $assistant->getAvailability();
                arsort($availabilitySorted);

                foreach ($this->schools as $school) {
                    if ($school->getName() == $assistant->getAssignedSchool()) continue;
                    foreach ($availabilitySorted as $bestDay => $score) {
                        if ($this->capacityLeftOnDay($bestDay, $school) == 0) {
                            continue;
                        }
                        if ($score == 0) break;
                        if (!array_key_exists($bestDay, $school->getAssistants())) continue;
                        if (sizeof($this->getAllAssistantOnSchoolByDay($school, $bestDay)) != 1) continue;
                        foreach ($this->getAllAssistantOnSchoolByDay($school, $bestDay) as $assistantInSchool) {
                            if($this->isInLockedList($assistantInSchool) !== null)continue;
                            if ($this->capacityLeftOnDay($assistantInSchool->getAssignedDay(), $this->getSchoolByName($assistant->getAssignedSchool())) < 1) continue;
                            if ($this->capacityLeftOnDay($assistant->getAssignedDay(), $this->getSchoolByName($assistantInSchool->getAssignedSchool())) < 1) continue;
                            if ($assistantInSchool->getAssignedSchool() == $assistant->getAssignedSchool()) continue;
                            $evalScoreBeforeSwap = $this->evaluate();
                            $this->swapAssistants($assistantInSchool, $assistant);
                            $evalScoreAfterSwap = $this->evaluate();

                            if ($evalScoreAfterSwap > $evalScoreBeforeSwap) {
                                $changed = true;
                            } else {
                                $this->swapAssistants($assistantInSchool, $assistant);
                            }
                        }
                    }
                }
            }
        }

        //Try to move assistants to other schools
        $changed = true;
        while ($changed) {

            $changed = false;
            foreach ($this->assistants as $assistant) {
                if($this->isInLockedList($assistant) !== null)continue;
                foreach ($this->schools as $school) {
                    $day = $assistant->getAssignedDay();
                    if (!array_key_exists($day, $school->getAssistants())) continue;
                    if ($this->capacityLeftOnDay($day, $school) == 0) continue;
                    $oldSchool = $assistant->getAssignedSchool();
                    $evalScoreBeforeMove = $this->evaluate();
                    $this->moveAssistant($assistant, $school->getName(), $day, $day);
                    $evalScoreAfterMove = $this->evaluate();
                    if ($evalScoreAfterMove > $evalScoreBeforeMove) {
                        $changed = true;
                    } else {
                        $this->moveAssistant($assistant, $oldSchool, $day, $day);
                    }
                }
            }
        }
        $this->improveTime = (round(microtime(true) * 1000) - $startTime) / 1000;
    }

    /**
     * @return int|int in [0,100]
     */
    public
    function evaluate()
    {
        //TODO: Adjust points to achieve wanted results
        if (sizeof($this->assistants) === 0) {
            return 0;
        }
        $maxPoints = 0;
        $points = 0;

        //Checks if you are placed on a good day
        foreach ($this->assistants as $assistant) {
            $avP = $assistant->getAvailability()[$assistant->getAssignedDay()];
            if ($avP == 2) {
                $points += 100;
            } elseif ($avP == 1) {
                $points += 90;
            } else {
                $points += 0;
            }
            $maxPoints += 100;
        }

        //Check if every school has at least one assistant
        foreach ($this->schools as $school) {
            $maxPoints += 200;
            if ($school->hasAssistants()) {
                $points += 200;
            }

            //Check if assistant is alone
            foreach ($school->getAssistants() as $day => $amount) {

                if ($amount > 0) {
                    $maxPoints += 300;
                    if ($amount >= 2) {
                        $points += 300;
                    }else{
                    }
                }
            }
        }
        if ($maxPoints == 0) return 0;
        return 100 * $points / $maxPoints;
    }

    /*
     * Checks if allocation breaks any important rule
     */
    public function isOk()
    {
        if (sizeof($this->assistants) === 0) {
            return false;
        }
        foreach ($this->assistants as $assistant) {
            if ($assistant->getAvailability()[$assistant->getAssignedDay()] == 0) return false;
        }
        foreach ($this->schools as $school) {
            if (!$school->hasAssistants()) {
                return false;
            }

            foreach ($school->getAssistants() as $day => $amount) {
                if ($amount == 1) return false;
            }
        }
        return true;
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

                    //TODO: Figure out if this is a good idea
                    if ($school->totalAssistants() > $this->getSchoolByName($assistant->getAssignedSchool())->totalAssistants()) continue;

                    //Create a deep copy of the current allocation
                    $newAllocation = $this->deepCopy();
                    //Move the assistant from current school to a new school and add the new allocation to the neighbour list
                    $newAllocation->moveAssistant($newAllocation->getAssistants()[$assistantIndex], $newAllocation->getSchools()[$schoolIndex]->getName(), $assistant->getAssignedDay(), $day);
                    Allocation::$visited++;
                    $neighbours[] = $newAllocation;
                }
                $schoolIndex++;
            }
            $assistantIndex++;
        }
        return $neighbours;
    }

    public function getAssistantById($id, $assistants){
        foreach($assistants as $assistant){
            if($assistant->getId() == $id)return $assistant;
        }
        return null;
    }

    public function isInLockedList($assistant){
        foreach($this->lockedAssistants as $lockedAssistant){
            if($assistant->getId() == $lockedAssistant->getId())return $lockedAssistant;
        }
        return null;
    }

    public
    function capacityLeftOnDay($day, School $school)
    {
        $assistants = $school->getAssistants();
        return array_key_exists($day, $assistants) ? $school->getCapacity()[$day] - $assistants[$day] : $school->getCapacity()[$day];
    }

    public
    function addAssistantToSchool(School $school, $day)
    {
        $school->addAssistant($day);
    }

    public function sortSchoolsByNumberOfAssistants($day)
    {
        $sortedSchools = array();
        $tempSorted = array();//Array with key = totalNumber of assistants on school, value = array with schools
        foreach ($this->schools as $school) {
            $index = $school->totalAssistants();
            if (!array_key_exists($index, $tempSorted)) $tempSorted[$index] = array();
            $tempSorted[$index][] = $school;
        }
        foreach($tempSorted as $schoolArray){
            usort($schoolArray, function($a, $b) use ($day){
                return $a->capacityLeftOnDay($day) > $b->capacityLeftOnDay($day);
            });
        }

        ksort($tempSorted);
        $toBeSorted = array();
        foreach ($tempSorted as $temp) {
            foreach ($temp as $school) {
                if (array_key_exists($day, $school) && $school->getAssistants()[$day] == 1) {
                    $sortedSchools[] = $school;
                } else {
                    $toBeSorted[] = $school;
                }
            }
        }
        $i = 0;
        foreach ($toBeSorted as $school) {
            if (array_key_exists($day, $school) && $school->getAssistants()[$day] == 0) {
                $sortedSchools[] = $school;
                unset($toBeSorted[$i]);
            }
            $i++;
        }
        while (!empty($toBeSorted)) {
            $bestSchool = null;
            $bestCapacity = 0.0;
            $added = false;
            foreach ($toBeSorted as $school) {
                if (array_key_exists($day, $school) && ($this->capacityLeftOnDay($day, $school) / $school->getCapacity()[$day]) > $bestCapacity) {
                    $bestSchool = $school;
                    $bestCapacity = $this->capacityLeftOnDay($day, $school) / $school->getCapacity()[$day];
                    $added = true;
                }
            }
            if ($bestSchool !== null) {
                $sortedSchools[] = $bestSchool;
                unset($toBeSorted[$key = array_search($toBeSorted, $bestSchool)]);
            } elseif (!$added) {
                $keys = array_keys($toBeSorted);
                $key = $keys[0];
                $sortedSchools[] = $toBeSorted[$key];
                unset($toBeSorted[$key]);
            }

        }
        return $sortedSchools;

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
    function swapAssistants(Assistant $a, Assistant $b)
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
        $newAllocation = new Allocation($schoolsCopy, $assistantsCopy);
        return $newAllocation;
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