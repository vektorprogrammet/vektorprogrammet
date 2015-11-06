<?php
namespace AppBundle\Entity\SimulatedAnnealing;

class Solution
{
    private $schools;
    private $assistants;

    /**
     * Solution constructor.
     * @param mixed schools
     * @param mixed assistants
     */
    public function __construct($schools)
    {
        $this->assistants = array();
        $this->schools = $schools;
    }

    public function initializeSolution($assistants){
        foreach($assistants as $assistant){
            $availabilitySorted = $assistant->getAvailability();
            arsort($availabilitySorted);
            $assistant->setAvailability($availabilitySorted);
            $i = 0;
            $bestSchool = null;
            $bestDay = null;
            while($bestSchool === null){
                if($i > 4) break; //If there is no capacity left in any school
                $bestDay = array_keys($availabilitySorted)[$i];
                foreach($this->schools as $school){
                    $capacityOnBestDay = $this->capacityLeftOnDay($bestDay, $school);
                    if($bestSchool === null && $capacityOnBestDay>0){
                        $bestSchool = $school;
                        continue;
                    }elseif($bestSchool !== null){
                        $currentBestCapacityOnBestDay = $bestSchool->getCapacity()[$bestDay];
                        if($capacityOnBestDay > $currentBestCapacityOnBestDay){
                            $bestSchool = $school;
                        }
                    }
                }
                $i++;
            }
            if($i > 4) break;//If there is no capacity left in any school
            $assistant->setAssignedSchool($bestSchool);
            $assistant->setAssignedDay($bestDay);
            $this->addAssistantToSchool($bestSchool, $assistant, $bestDay);
            $this->assistants[]=$assistant;
        }
    }


    public function evaluate(){
        if(sizeof($this->assistants) === 0){return 0;}
        $points = 0;
        foreach($this->assistants as $assistant){
            $points += $assistant->getAvailability()[$assistant->getAssignedDay()];
        }
        return 100*$points/(2*sizeof($this->assistants));
    }

    private  function capacityLeftOnDay($day, $school){
        return array_key_exists($day,$school->getAssistants()) ? $school->getCapacity()[$day] - sizeof($school->getAssistants()[$day]) : $school->getCapacity()[$day];
    }

    public function addAssistantToSchool($school, $assistant, $day){
        $index = -1;
        for($i = 0; $i < sizeof($this->schools); $i++){
            if($school === $this->schools[$i]){
                $index = $i;
                break;
            }
        }
        //$index = array_search($school, $this->schools);
        $school->addAssistant($assistant, $day);
        $this->schools[$index] = $school;
    }

    public function moveAssistant(Assistant $assistant, School $school, $fromDay, $toDay){
        $assistant->getAssignedSchool()->removeAssistant($assistant, $fromDay);
        $assistant->setAssignedSchool($school);
        $assistant->setAssignedDay($toDay);
        $this->addAssistantToSchool($school, $assistant, $toDay);
    }

    public function addSchool($school){
        $this->schools[] = $school;
    }

    public function deepCopySchools(){
        $copy = array();
        foreach($this->schools as $school){
            $copy[] = $school->deepCopy();
        }

        return $copy;
    }

    public function deepCopyAssistants($schools){
        $copy = array();
        foreach($this->assistants as $assistant){
            $school = $assistant->getAssignedSchool();
            $schoolCopy = null;
            foreach($schools as $s){
                if($s->getName() === $school->getName()){
                    $schoolCopy = $s;
                    break;
                }
            }
            $copy[] = $assistant->deepCopy($schoolCopy);
        }
        return $copy;
    }

    public function replaceSchool($oldSchool, $newSchool){
        $this->schools[array_search($oldSchool, $this->schools)] = $newSchool;
    }

    public function removeSchool($school){
        unset($this->schools[array_search($school, $this->schools)]);
    }

    public function addAssistant($assistant){
        $this->assistants[]=$assistant;
    }

    public function removeAssistant($assistant){
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