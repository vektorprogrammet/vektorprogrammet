<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Solution
{
    private $schools;//Array with School objects
    private $assistants;//Array with Assistant objects
    public $initializeTime;
    public $improveTime;
    public $optimizeTime;
    private $toBeImproved;

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
        $this->schools = $schools;
    }

    public function initializeSolution($optimize = false, $sort = false)
    {
        $startTime = round(microtime(true) * 1000);
        foreach ($this->assistants as $assistant) {
            if (!$optimize) {

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
            } else {
                //Sort availability lists, best day first.
                $availabilitySorted = $assistant->getAvailability();
                if($sort){
                    arsort($availabilitySorted);
                }
                $assistant->setAvailability($availabilitySorted);

                $i = 0;
                $bestSchool = null;
                $bestDay = null;

                while ($bestSchool === null) {

                    if ($i > 4) break; //If there is no capacity left in any school

                    $bestDay = array_keys($availabilitySorted)[$i];
                    foreach ($this->schools as $school) {
                        $capacityOnBestDay = $this->capacityLeftOnDay($bestDay, $school);
                        //If no bestSchool has been set yet and there is capacity on this school
                        if ($bestSchool === null && $capacityOnBestDay > 0) {
                            $bestSchool = $school;
                            continue;
                        } elseif ($bestSchool !== null) {
                            //Find the best school. The best school will be the school with most capacity left on the weekday that is best for the assistant.
                            $currentBestCapacityOnBestDay = $bestSchool->getCapacity()[$bestDay];
                            if ($capacityOnBestDay > $currentBestCapacityOnBestDay) {
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

    }


    public function improveSolution()
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
     * @return float|int in [0,100]
     */
    public function evaluate()
    {
        if (sizeof($this->assistants) === 0) {
            return 0;
        }
        $points = 0;
        foreach ($this->assistants as $assistant) {
            $points += $assistant->getAvailability()[$assistant->getAssignedDay()];
        }
        return 100 * $points / (2 * sizeof($this->assistants));
    }

    private function capacityLeftOnDay($day, $school)
    {
        return array_key_exists($day, $school->getAssistants()) ? $school->getCapacity()[$day] - $school->getAssistants()[$day] : $school->getCapacity()[$day];
    }

    public function addAssistantToSchool($school, $day)
    {
        $school->addAssistant($day);
    }

    private function getAllAssistantOnSchoolByDay(School $school, $day)
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

    public function swampAssistants(Assistant $a, Assistant $b)
    {
        $aSchool = $a->getAssignedSchool();
        $aDay = $a->getAssignedDay();
        $bSchool = $b->getAssignedSchool();
        $bDay = $b->getAssignedDay();

        $this->moveAssistant($a, $bSchool, $aDay, $bDay);
        $this->moveAssistant($b, $aSchool, $bDay, $aDay);

    }

    public function moveAssistant(Assistant $assistant, $schoolName, $fromDay, $toDay)
    {
        $school = $this->getSchoolByName($assistant->getAssignedSchool());
        $newSchool = $this->getSchoolByName($schoolName);
        $school->removeAssistant($fromDay);
        $assistant->setAssignedSchool($schoolName);
        $assistant->setAssignedDay($toDay);
        $this->addAssistantToSchool($newSchool, $toDay);
    }

    private function getSchoolByName($schoolName)
    {
        foreach ($this->schools as $school) {
            if ($school->getName() === $schoolName) {
                return $school;
            }
        }
    }

    public function addSchool($school)
    {
        $this->schools[] = $school;
    }

    private function removeFromImprove(Assistant $assistant)
    {
        unset($this->toBeImproved[array_search($assistant, $this->toBeImproved)]);
    }

    public function deepCopySchools()
    {
        $copy = array();
        foreach ($this->schools as $school) {
            $copy[] = clone $school;
        }

        return $copy;
    }

    public function deepCopy()
    {
        $schoolsCopy = $this->deepCopySchools();
        $assistantsCopy = $this->deepCopyAssistants($this->schools);
        return new Solution($schoolsCopy, $assistantsCopy);
    }

    public function deepCopyAssistants($schools)
    {
        $copy = array();
        foreach ($this->assistants as $assistant) {
            $copy[] = clone $assistant;
        }
        return $copy;
        /*foreach ($this->assistants as $assistant) {
            $school = $this->getSchoolByName($assistant->getAssignedSchool());
            $schoolCopy = null;
            foreach ($schools as $s) {
                if ($s->getName() === $school->getName()) {
                    $schoolCopy = $s;
                    break;
                }
            }
            $copy[] = $assistant->deepCopy($schoolCopy);
        }
        return $copy;*/
    }

    public function replaceSchool($oldSchool, $newSchool)
    {
        $this->schools[array_search($oldSchool, $this->schools)] = $newSchool;
    }

    public function removeSchool($school)
    {
        unset($this->schools[array_search($school, $this->schools)]);
    }

    public function addAssistant($assistant)
    {
        $this->assistants[] = $assistant;
    }

    public function removeAssistant($assistant)
    {
        unset($this->assistants[array_search($assistant, $this->assistants)]);
    }

    /**
     * @return mixed
     */
    public function getSchools()
    {
        return $this->schools;
    }

    /**
     * @param mixed $schools
     */
    public function setSchools($schools)
    {
        $this->schools = $schools;
    }

    /**
     * @return mixed
     */
    public function getAssistants()
    {
        return $this->assistants;
    }

    /**
     * @param mixed $assistants
     */
    public function setAssistants($assistants)
    {
        $this->assistants = $assistants;
    }


}