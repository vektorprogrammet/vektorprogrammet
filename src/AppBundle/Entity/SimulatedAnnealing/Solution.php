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
    public function __construct($schools, $assistants)
    {
        $this->schools = $schools;
        $this->initializeSolution($assistants);
    }

    private function initializeSolution($assistants){
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
                    $capacityOnBestDay = $school->getCapacity()[$bestDay];
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
        $points = 0;
        foreach($this->assistants as $assistant){
            $points += ( 2 - $assistant->getAvailability()[$assistant->getAssignedDay()] );
        }
        return 100/(1+$points/100);
    }

    public function addAssistantToSchool($school, $assistant, $day){
        $index = array_search($school, $this->schools);
        $school->addAssistant($assistant, $day);
        $this->schools[$index] = $school;
    }

    public function addSchool($school){
        $this->schools[] = $school;
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