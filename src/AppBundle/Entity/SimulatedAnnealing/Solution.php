<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Solution
{
    private $schools;//Array with School objects
    private $assistants;//Array with Assistant objects
    private $assistantsInBolk1;
    private $assistantsInBolk2;
    private $lockedAssistants;
    public $initializeTime;
    public $improveTime;
    public $optimizeTime;
    private $toBeImproved;
    private $toBeImprovedBolk1;
    private $toBeImprovedBolk2;
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
        $this->assistantsInBolk1 = array();
        $this->assistantsInBolk2 = array();
        $this->lockedAssistants = array();
        $this->schools = $schools;
    }

    public function initializeSolution($optimize = false, $sort = false, $bolk2 = false)
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
                            dump($assistant->getName());
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
            if (!$bolk2) {
                $assistants = $this->assistantsInBolk1;
            } else {
                $assistants = $this->assistantsInBolk2;
            }

            foreach ($assistants as $assistant) {
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
                    foreach ($this->schools as $school) {
                        if ($school->getCapacity()[$bestDay] == 0) {
                            $capacityOnBestDay = 0;
                        } else {
                            $capacityOnBestDay = $this->capacityLeftOnDay($bestDay, $school, $bolk2) / $school->getCapacity()[$bestDay];
                            //dump("Total Capacity on ".$school->getName().": ".$school->getCapacity()[$bestDay].". Total Left: ".$this->capacityLeftOnDay($bestDay, $school));
                        }
                        //If no bestSchool has been set yet and there is capacity on this school
                        if ($bestSchool === null && $capacityOnBestDay > 0) {
                            $bestSchool = $school;
                            $bestDayCapacity = $capacityOnBestDay;
                            continue;
                        } elseif ($bestSchool !== null) {
                            //Find the best school. The best school will be the school with most capacity left on the weekday that is best for the assistant.
                            if ($capacityOnBestDay > $bestDayCapacity) {
                                $bestSchool = $school;
                            }
                        }
                    }
                    $i++;
                }
                if ($i > 5) break;//If there is no capacity left in any school

                //Update the assistant with the bestSchool and bestDay found and add it to the assistants list
                $debug = "Assigning ".$assistant->getName()." on day ". $bestDay. " to school, ".$bestSchool->getName().", in bolk ";
                if($bolk2) {
                    $debug .= "Bolk 2";
                }else{
                    $debug .= "Bolk 1";
                }
                //dump($debug);
                $assistant->setAssignedSchool($bestSchool->getName());
                $assistant->setAssignedDay($bestDay);
                $this->addAssistantToSchool($bestSchool, $bestDay, $bolk2);
                //$this->assistants[] = $assistant;
                if ($assistant->getAvailability()[$bestDay] !== 2) {
                    if(!$bolk2){
                        $this->toBeImprovedBolk1[] = $assistant;
                    }else{
                        $this->toBeImprovedBolk2[] = $assistant;
                    }
                }
            }
            if(!$bolk2){
                $this->initializeSolution($optimize, $sort, true);
            }
        }
        $this->initializeTime = (round(microtime(true) * 1000) - $startTime) / 1000;
        Solution::$visited = 1;
        //Solution::$visited[] = $this;

    }

    public
    function improveSolution($bolk2 = false)
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
                        foreach ($this->getAllAssistantOnSchoolByDay($school, $bestDay, $bolk2) as $assistantInSchool) {

                            if ($assistantInSchool->getAvailability()[$currentDay] >= $assistantInSchool->getAvailability()[$assistantInSchool->getAssignedDay()]) {
                                $this->swampAssistants($assistantInSchool, $assistant, $bolk2);
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
        if($bolk2){
            $this->improveSolution(true);
        }
        $this->improveTime = (round(microtime(true) * 1000) - $startTime) / 1000;
    }

    /**
     * @return int|int in [0,100]
     */
    public
    function evaluate()
    {
        if (sizeof($this->assistants) === 0) {
            return 0;
        }
        $maxPoints = 0;
        $points = 0;
        foreach ($this->assistantsInBolk1 as $assistant) {
            $avP = $assistant->getAvailability()[$assistant->getAssignedDay()];
            if ($avP == 2) {
                $points += 5;
            } elseif ($avP == 1) {
                $points += 3;
            }
            $maxPoints += 5;
        }
        foreach ($this->assistantsInBolk2 as $assistant) {
            $avP = $assistant->getAvailability()[$assistant->getAssignedDay()];
            if ($avP == 2) {
                $points += 5;
            } elseif ($avP == 1) {
                $points += 3;
            }
            $maxPoints += 5;
        }
        foreach ($this->schools as $school) {
            $maxPoints += 10;
            if ($school->hasAssistants()) {
                $points += 10;
            }

            foreach ($school->getAssistants() as $amount) {
                if ($amount > 0) {
                    $maxPoints += 5;
                    if ($amount >= 2) {
                        $points += 5;
                    }
                }
            }
        }
        if ($maxPoints == 0) return 0;
        return 100 * $points / $maxPoints;
    }

    public
    function generateNeighbours($bolk2 = false)
    {
        $neighbours = array();
        $assistantIndex = 0;
        if(!$bolk2){
            $assistants = $this->assistantsInBolk1;
        }else{
            $assistants = $this->assistantsInBolk2;
        }
        foreach ($assistants as $assistant) {
            $schoolIndex = 0;
            foreach ($this->getSchools() as $school) {
                foreach ($school->getCapacity() as $day => $capacity) {
                    if ($capacity === 0) continue;
                    //Check if the school has capacity for more assistants
                    $cap = array_key_exists($day, $school->getAssistants($bolk2)) ? $school->getAssistants($bolk2)[$day] : 0;
                    $freeCapacity = $capacity - $cap;
                    if ($freeCapacity < 1) continue;

                    //Create a deep copy of the current solution
                    $schoolsCopy = $this->deepCopySchools();
                    $assistantsCopy = $this->deepCopyAssistants();
                    $bolk1Copy = $this->deepCopyBolk1Assistants();
                    $bolk2Copy = $this->deepCopyBolk2Assistants();
                    $newSolution = new Solution($schoolsCopy, $assistantsCopy);
                    $newSolution->setAssistantsInBolk1($bolk1Copy);
                    $newSolution->setAssistantsInBolk2($bolk2Copy);
                    //Move the assistant from current school to a new school and add the new solution to the neighbour list
                    $newSolution->moveAssistant($assistantsCopy[$assistantIndex], $schoolsCopy[$schoolIndex]->getName(), $assistant->getAssignedDay(), $day, $bolk2);
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
    function divideBolks()
    {
        foreach ($this->assistants as $assistant) {
            if ($assistant->isDoublePosition()) {
                $this->assistantsInBolk1[] = $assistant;
                $this->assistantsInBolk2[] = $assistant;
                $this->lockedAssistants[] = $assistant;
                $assistant->assignBothBolks();
            }
        }
        foreach ($this->assistants as $assistant) {
            if ($assistant->isDoublePosition()) continue;
            if ($assistant->isPrefBolk1()) {
                $this->assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
            } elseif ($assistant->isPrefBolk2()) {
                $this->assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
            } elseif (sizeof($this->assistantsInBolk1) > sizeof($this->assistantsInBolk2)) {
                $this->assistantsInBolk2[] = $assistant;
                $assistant->assignBolk2();
            } else {
                $this->assistantsInBolk1[] = $assistant;
                $assistant->assignBolk1();
            }
        }
    }

    private
    function capacityLeftOnDay($day, $school, $bolk2 = false)
    {
        if(!$bolk2){
            $assistants = $school->getBolk1Assistants();
        }else{
            $assistants = $school->getBolk2Assistants();
        }
        return array_key_exists($day, $assistants) ? $school->getCapacity()[$day] - $assistants[$day] : $school->getCapacity()[$day];
    }

    public
    function addAssistantToSchool($school, $day, $bolk2 = false)
    {
        $school->addAssistant($day, $bolk2);
    }

    private
    function getAllAssistantOnSchoolByDay(School $school, $day, $bolk2 = false)
    {
        $assistants = array();
        $schoolName = $school->getName();
        if(!$bolk2){
            $assArray = $this->assistantsInBolk1;
        }else{
            $assArray = $this->assistantsInBolk2;
        }
        foreach ($assArray as $assistant) {
            if ($assistant->getAssignedSchool() === $schoolName && $assistant->getAssignedDay() === $day) {
                $assistants[] = $assistant;
            }
        }
        return $assistants;
    }

    public
    function swampAssistants(Assistant $a, Assistant $b, $bolk2 = false)
    {
        $aSchool = $a->getAssignedSchool();
        $aDay = $a->getAssignedDay();
        $bSchool = $b->getAssignedSchool();
        $bDay = $b->getAssignedDay();

        $this->moveAssistant($a, $bSchool, $aDay, $bDay, $bolk2);
        $this->moveAssistant($b, $aSchool, $bDay, $aDay, $bolk2);

    }

    public
    function moveAssistant(Assistant $assistant, $schoolName, $fromDay, $toDay, $bolk2 = false)
    {
        $school = $this->getSchoolByName($assistant->getAssignedSchool());
        $newSchool = $this->getSchoolByName($schoolName);
        $school->removeAssistant($fromDay, $bolk2);
        $assistant->setAssignedSchool($schoolName);
        $assistant->setAssignedDay($toDay);
        $this->addAssistantToSchool($newSchool, $toDay, $bolk2);
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
        $assistantsCopy = $this->deepCopyAssistantList();
        $newSolution = new Solution($schoolsCopy, $assistantsCopy);
        $newSolution->setAssistantsInBolk1($this->deepCopyBolk1Assistants());
        $newSolution->setAssistantsInBolk2($this->deepCopyBolk2Assistants());
        return $newSolution;
    }

    public function deepCopyAssistantList(){
        $copy = array();
        foreach ($this->assistants as $assistant) {
            $copy[] = clone $assistant;
        }
        return $copy;
    }

    public function deepCopyAssistants()
    {
        $copy = array();
        foreach ($this->assistantsInBolk1 as $assistant) {
            $copy[] = clone $assistant;
        }
        foreach ($this->assistantsInBolk2 as $assistant) {
            $copy[] = clone $assistant;
        }
        return $copy;
    }

    public function deepCopyBolk1Assistants()
    {
        $copy = array();
        foreach ($this->assistantsInBolk1 as $assistant) {
            $copy[] = clone $assistant;
        }
        return $copy;
    }

    public function deepCopyBolk2Assistants()
    {
        $copy = array();
        foreach ($this->assistantsInBolk2 as $assistant) {
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
    function getAssistantsInBolk1()
    {
        return $this->assistantsInBolk1;
    }

    /**
     * @param array $assistantsInBolk1
     */
    public
    function setAssistantsInBolk1($assistantsInBolk1)
    {
        $this->assistantsInBolk1 = $assistantsInBolk1;
    }

    /**
     * @return array
     */
    public
    function getAssistantsInBolk2()
    {
        return $this->assistantsInBolk2;
    }

    /**
     * @param array $assistantsInBolk2
     */
    public
    function setAssistantsInBolk2($assistantsInBolk2)
    {
        $this->assistantsInBolk2 = $assistantsInBolk2;
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